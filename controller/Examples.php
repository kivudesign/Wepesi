<?php
    class Examples{

        function echo($id=false){
            $list=[
                [
                    "id"=>1,
                    "name"=>"monday",
                    "qte"=>1],
                [
                    "id"=>2,
                    "name"=>"tuesday",
                    "qte"=>2],
                [
                    "id"=>3,
                    "name"=>"saturday",
                    "qte"=>6],
                [
                    "id"=>4,
                    "name"=>"sunday",
                    "qte"=>7],
            ];
            $len=count($list);
            if($id){            
                foreach ($list as $key => $value) {
                    if ($value['id'] == $id) {
                        return $list[$key];
                    } else if ($key == ($len - 1) && $value['id'] != $id) {
                        Session::put('warning', 3);
                        return false;
                    }
                }
            }                
            return $list;
        }

    }
?>