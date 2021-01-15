<?php
    class Home{
        function __construct()
        {
            $this->db= DB::getInstance();
        }
        function welcom(){
            $req=$this->db->get("users")->result();
            return $req;
            // return "welcom to home class methode";
        }
    }