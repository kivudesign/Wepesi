<?php
    class Response{
        static function send(array $data){
            header('Content-Type:application/json;chartset=utf-8');
            echo json_encode($data,true);
            exit();
        }
    }