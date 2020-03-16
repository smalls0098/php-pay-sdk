<?php
declare (strict_types=1);

namespace Smalls\Pay;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:36
 **/

/**
 * @method static Event dispatch(Event $event)                                Dispatches an event to all registered listeners
 * @method static array getListeners($eventName = null)                       Gets the listeners of a specific event or all listeners sorted by descending priority.
 * @method static int|void getListenerPriority($eventName, $listener)         Gets the listener priority for a specific event.
 * @method static bool hasListeners($eventName = null)                        Checks whether an event has any registered listeners.
 * @method static void addListener($eventName, $listener, $priority = 0)      Adds an event listener that listens on the specified events.
 * @method static removeListener($eventName, $listener)                       Removes an event listener from the specified events.
 * @method static void addSubscriber(EventSubscriberInterface $subscriber)    Adds an event subscriber.
 * @method static void removeSubscriber(EventSubscriberInterface $subscriber)
 */
class Events
{

    protected static $dispatcher;


    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::getDispatcher(), $method], $args);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([self::getDispatcher(), $method], $args);
    }

    public static function setDispatcher(EventDispatcher $dispatcher)
    {
        self::$dispatcher = $dispatcher;
    }

    public static function getDispatcher(): EventDispatcher
    {
        if (self::$dispatcher) {
            return self::$dispatcher;
        }

        return self::$dispatcher = self::createDispatcher();
    }

    public static function createDispatcher(): EventDispatcher
    {
        return new EventDispatcher();
    }

}