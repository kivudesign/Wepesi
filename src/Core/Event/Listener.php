<?php

namespace Wepesi\Core\Event;

/**
 *
 */
class Listener
{
    /**
     * @var bool
     */
    public bool $stopPropagation = false;
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var int
     */
    private int $priority;
    /**
     * @var int
     */
    private int $once;
    /**
     * @var int
     */
    private int $calls = 0;

    /**
     * @param callable $callback
     * @param int $priority
     */
    function __construct(callable $callback, int $priority)
    {
        $this->callback = $callback;
        $this->priority = $priority;
    }

    /**
     * @param array $args
     * @return mixed|null
     */
    function handle(array $args)
    {
        if ($this->once && $this->calls > 0) {
            return null;
        }
        $this->calls++;
        return call_user_func_array($this->callback, $args);
    }

    /**
     * @return $this
     */
    function once(): Listener
    {
        $this->once = true;
        return $this;
    }

    /**
     * @return int
     */
    function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return $this
     */
    function stopPropagation(): Listener
    {
        $this->stopPropagation = true;
        return $this;
    }
}