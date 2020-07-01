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
            $detail=false;
            $len=count($list);
            if($id){
                $detail=true;          
                foreach ($list as $key => $value) {
                    if ($value['id'] == $id) {
                        return [$list[$key]];
                    } else if ($key == ($len - 1) && $value['id'] != $id) {
                        Session::put('warning', 3);
                        return false;
                    }
                }
            }
            if($detail){
                $view = new View('exemple');
                $view->assign("data", $list);
            }else{
                return $list;
            }
                
        }

        function addList($req){
            var_dump($req);
            // Redirect::to("/");
        }

    }
?>