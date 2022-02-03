<?php

/*
    Run cron notes:
    There are 2 types of cron hooks in the wordpress system.
    Continuously-Scheduled & One-Time-Run

    Continuously-Scheduled:
        * will have a "schedule" attribute, type:string & not-empty
        * will have an "interval" attribute, type:int and greater than 0

    One-Time-Run:
        One-Time-Run can have multiple different states depending on the system
        that is inserting the cron (IE: Crontrol Plugin, WooCommerce, etc).
        State 1:
            * will have "schedule" attribute, type:string & empty
            * will not have "interval" attribute
        State 2:
            * will have a "schedule" attribute, type:bool value:false
            * will have an "interval" attribute, type:int value: 0


*/

require_once __DIR__ . '/auth.php';

define('AUTOMATIC_UPDATER_DISABLED', true);
define('DISABLE_WP_CRON', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
require_once __DIR__ . '/mwp.includes.php';

//=====
// 
// check incoming query string has all required parameters
if (!isset($_GET['hook']) || !isset($_GET['ts']) || !isset($_GET['hash'])) {
    http_response_code(400);
    die("bad parameters");
}

$time_begin = microtime(true);

// extract cron vars
$hookname   = $_GET['hook'];
$timestamp  = (int)$_GET['ts'];
$hash       = $_GET['hash'];
$method     = $_SERVER['REQUEST_METHOD'];

$crons = get_option('cron');

$time_get_crons = microtime(true);

if (!isset($crons[$timestamp][$hookname][$hash])) {
    http_response_code(404);
    die("target not found | ${hookname} - ${timestamp} - ${hash}");
}

$hook = $crons[$timestamp][$hookname][$hash];

$payload = [
    'hook'   => $hookname,
    'ts'     => $timestamp,
    'hash'   => $hash,
    'result' => false,
    'timings' => [
        'getCrons' => (float)sprintf("%f", ($time_get_crons - $time_begin)),
    ]
];


if ($method === 'POST') {

    $payload['action'] = 'run';

    // measure cron exec time
    $time_start_cron = microtime(true);

    // run cron without $wp_filter[all]
    mwp_do_action_direct($hookname, $hook['args']);

    // calc cron exec time
    $payload['timings']['execCron'] = (float)sprintf("%f", (microtime(true) - $time_start_cron));


    // TODO write result of run to output

    // TODO just modify 'crons' and call update_option('cron', $crons);
    // FIXME this needs to be an atomic update, or work with silly transients

    // measure updating cron schedule
    $time_start_update = microtime(true);

    // delete the cron we just ran.
    // If a future run is to be scheduled, we'll use info in memory ($hook)
    // We store the removal of the cron as our result, in-case this is a one-time-run
    // and no further updates are required.
    $del_event = wp_unschedule_event($timestamp, $hookname, $hook['args']);
    if (is_bool($del_event)) { // check if return is bool. WP_Error can be returned for errors
        $payload['result'] = $del_event;
    } else { // if bool not returned, result is false
        $payload['result'] = false;
    }

    // re-query crons so we have the latest object graph
    $crons = get_option('cron');

    // GOTCHA NOTE:
    // if we received a $payload['result'] = false, this may not be technically correct,
    // some woocommerce events such as `woocommerce_cancel_unpaid_orders` is a 
    // non-repeating event, however, as part of it's execution, it re-schedules
    // itself to run hourly (without an interval), this would cause the above 
    // wp_unschedule_event() to return a `false` due to the timestamp not matching 
    // the now re-scheduled event in `get_option('cron')`. 
    // https://github.com/woocommerce/woocommerce/blob/313d40d3960da3de560a96566491bb6115141eec/plugins/woocommerce/includes/wc-order-functions.php#L910
    //
    // To factor in this edge-case, we will check our freshly queried $crons list
    // for the event that was to be deleted if our $payload['result'] = false, if the
    // event is not present, we will revert to $payload['result'] = true as the event
    // no longer exists under its executed parameters (just as if it was successfully un-scheduled).
    if ($payload['result'] == false) {
        if (!isset($crons[$timestamp][$hookname][$hash])) {
            // cron not found under its executed parameters,
            // treat the previous `wp_unschedule_event` as successful
            $payload['result'] = true;
        }
    }


    // check if the hook that executed is eligible for rescheduling
    if (is_string($hook['schedule']) && !empty($hook['schedule']) && isset($hook['interval']) && is_int($hook['interval'])) {

        // enforce min interval
        $interval = $hook['interval'];
        if ($interval < 60) {
            $interval = 60;
        }

        // update timestamp + interval for next run
        // $nextRun = $timestamp + $interval;
        // update using now() vs incoming timestamp
        $nextRun = time() + $interval;


        // add the cron at its next run
        $crons[$nextRun][$hookname][$hash] = $hook;

        // ensure crons are sorted by timestamp
        uksort($crons, 'strnatcasecmp');

        // overwrite 'cron' option with the modified/rescheduled cron object graph
        $payload['result'] = update_option('cron', $crons);
    }

    // format crons for cron-scheduler
    $payload['crons'] = mwp_format_crons($crons);

    $payload['timings']['updateCrons'] = (float)sprintf("%f", (microtime(true) - $time_start_update));
} else if ($method === 'DELETE') {

    $payload['action'] = 'delete';

    $time_start_update = microtime(true);
    // delete cron
    $payload['result'] = wp_unschedule_event($timestamp, $hookname, $hook['args']);

    // calc delete timing
    $payload['timings']['deleteCron'] = (float)sprintf("%f", (microtime(true) - $time_start_update));

    // fill response with the rest of the cron schedule
    $payload['crons'] = mwp_format_crons(get_option('cron'));
}

header("Cache-Control: no-cache");
header("Content-type: application/json");

echo json_encode($payload, JSON_PRETTY_PRINT);
