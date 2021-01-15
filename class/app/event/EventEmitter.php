<?php

class EventEmitter{
    private static $_instance;
    private $listeners=[];

    static function getInstance(): EventEmitter{
        if(!self::$_instance){
            self::$_instance= new self();
        }
        return self::$_instance;
    }

    function emit(string $event,...$args){
        if ($this->hasListeners($event)) {
            foreach ($this->listeners[$event] as $listener) {
                $listener->handle($args);
                if($listener->stopPropagation){
                    break;
                }
            }
        }
    }
    
    function on(string $event,callable $callback,int $priority=0):Listener{
        if(!$this->hasListeners($event)){
            $this->listeners[$event]=[];
        }
        $listener= new Listener($callback, $priority);
        $this->listeners[$event][]= $listener;
        $this->sortListener($event);
        return $listener;
    }
    function once(string $event,callable $callback,int $priority=0):Listener{
        
        return $this->on($event,$callback,$priority)->once();
    }

    private function hasListeners(string $event):bool{
        return array_key_exists($event, $this->listeners);
    }

    private function sortListener($event){
        uasort($this->listeners[$event],function($a,$b){
            return $a->getPriority()<$b->getPriority();
        });
    }
}