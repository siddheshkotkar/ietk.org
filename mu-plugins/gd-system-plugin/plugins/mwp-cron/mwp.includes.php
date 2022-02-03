<?php

/**
 * Max number of cron hooks to accept
 */
define("MWP_CRON_MAX_HOOKS", 50);

/**
 * comma separated string of blacklisted crons
 * NOTE: csv will be exploded into an array in mwp_format_crons().
 * 		 we want these in a constant to prevent overriding of these values
 */
define("MWP_BLACKLIST_CRONS", "");

/**
 * Run do_action on the incoming hookname without running 'all' filter.
 * https://developer.wordpress.org/reference/functions/do_action_ref_array/#source
 */
function mwp_do_action_direct($hookname, $args = [])
{
    global $wp_filter, $wp_actions, $wp_current_filter;

    // handle action counter
    if (!isset($wp_actions[$hookname])) {
        $wp_actions[$hookname] = 1;
    } else {
        ++$wp_actions[$hookname];
    }

    // if not found did user fail to 
    // `add_action(name, callback)`?
    if (!isset($wp_filter[$hookname])) {
        // wp implementation just returns
        return;
    }

    // push current filter
    $wp_current_filter[] = $hookname;

    // execute cron action
    $wp_filter[$hookname]->do_action($args);

    // remove current filter
    array_pop($wp_current_filter);
}

/**
 * Flatten wp_options['cron'] array of scheduled crons into
 * a single dimension array.
 * Also filter out crons deemed invalid or permanently blacklisted.
 *
 */
function mwp_format_crons($cron_option = [])
{

    $crons = [];

    // build blacklist array from csv
    $blacklist = explode(",", MWP_BLACKLIST_CRONS);
    // trim any leading/trailing whitespace
    foreach ($blacklist as $idx => $cron) {
        $blacklist[$idx] = trim($cron);
    }

    foreach ($cron_option as $timestamp => $cronhooks) {

        if (!is_int($timestamp)) {
            continue;
        }

        foreach ($cronhooks as $hookname => $keys) {

            if (!is_string($hookname)) {
                continue;
            }

            // check blacklist
            if (in_array($hookname, $blacklist)) {
                continue;
            }

            foreach ($keys as $hash => $hook) {
                if (!is_string($hash)) {
                    continue;
                }

                // handle schedule (bool)
                // if schedule is a bool(false) this is a one time run cron, clear it
                if (is_bool($hook['schedule'])) {
                    $hook['schedule'] = "";
                }

                // check for cron interval's under 60sec.
                // NOTE: single run crons have interval=0 & schedule="" (IE: empty string)
                if (isset($hook['interval']) && $hook['interval'] < 60 && strlen($hook['schedule']) > 0) {
                    continue;
                }

                $hook['nextRun'] = (int)$timestamp;
                $hook['name'] = $hookname;
                $hook['hash'] = $hash;

                /*
                // TODO maybe sanity check number of crons (if there are more than X, return error)
                if ( count($crons) > (int)constant(MWP_CRON_MAX_HOOKS) ) {

                }	
                 */

                $crons[] = $hook;
            }
        }
    }

    return $crons;
}
