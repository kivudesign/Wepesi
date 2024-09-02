<?php

namespace Wepesi\Core\Event;

/**
 *
 */
class EventEmitter
{
    /**
     * @var EventEmitter
     */
    private static EventEmitter $instance;
    /**
     * @var array
     */
    private array $listeners = [];

    /**
     * @return EventEmitter
     */
    static function getInstance(): EventEmitter
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $event
     * @param ...$args
     * @return void
     */
    function emit(string $event, ...$args)
    {
        if ($this->hasListeners($event)) {
            foreach ($this->listeners[$event] as $listener) {
                $listener->handle($args);
                if ($listener->stopPropagation) {
                    break;
                }
            }
        }
    }

    /**
     * @param string $event
     * @return bool
     */
    private function hasListeners(string $event): bool
    {
        return array_key_exists($event, $this->listeners);
    }

    /**
     * @param string $event
     * @param callable $callback
     * @param int $priority
     * @return Listener
     */
    function once(string $event, callable $callback, int $priority = 0): Listener
    {

        return $this->on($event, $callback, $priority)->once();
    }

    /**
     * @param string $event
     * @param callable $callback
     * @param int $priority
     * @return Listener
     */
    function on(string $event, callable $callback, int $priority = 0): Listener
    {
        if (!$this->hasListeners($event)) {
            $this->listeners[$event] = [];
        }
        $listener = new Listener($callback, $priority);
        $this->listeners[$event][] = $listener;
        $this->sortListener($event);
        return $listener;
    }

    /**
     * @param $event
     * @return void
     */
    private function sortListener($event)
    {
        uasort($this->listeners[$event], function ($a, $b) {
            return $a->getPriority() < $b->getPriority();
        });
    }
}