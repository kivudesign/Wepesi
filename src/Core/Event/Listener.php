<?php

namespace Wepesi\Core\Event;

class Listener
{
    public $stopPropagation = false;
    private $callback,
        $priority,
        $once,
        $calls = 0;

    function __construct(callable $callback, int $priority)
    {
        $this->callback = $callback;
        $this->priority = $priority;
    }

    function handle(array $args)
    {
        if ($this->once && $this->calls > 0) {
            return null;
        }
        $this->calls++;
        return call_user_func_array($this->callback, $args);
    }

    function once(): Listener
    {
        $this->once = true;
        return $this;
    }

    function getPriority()
    {
        return $this->priority;
    }

    function stopPropagation(): Listener
    {
        $this->stopPropagation = true;
        return $this;
    }
}