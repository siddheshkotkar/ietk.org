<?php

namespace GoDaddy\WordPress\MWC\Common\Events;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventContract;
use GoDaddy\WordPress\MWC\Common\Events\Exceptions\EventTransformFailedException;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

/**
 * Event transformers handler.
 */
class EventTransformers
{
    /**
     * Transforms the event based on the defined transformers in `events.transformers` configuration.
     *
     * @param EventContract $event
     * @throws EventTransformFailedException|Exception
     */
    public static function transform(EventContract $event)
    {
        try {
            foreach (static::getTransformers($event) as $transformerClass) {
                $transformer = new $transformerClass();
                if ($transformer->shouldHandle($event)) {
                    $transformer->handle($event);
                }
            }
        } catch (SentryException $exception) {
            // do nothing - the exception will be automatically reported to Sentry
        } catch (Exception $exception) {
            new EventTransformFailedException($exception->getMessage());
        }
    }

    /**
     * Gets a list of transformers for a given event.
     *
     * @param EventContract $event
     * @return string[] array of class names
     */
    public static function getTransformers(EventContract $event) : array
    {
        return ArrayHelper::get(Configuration::get('events.transformers'), get_class($event), []);
    }
}
