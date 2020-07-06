<?php
    class Examples{
        private $list=[
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
        function echo($id=false){
            $list=$this->list;
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
        function register(){
            $validate= new Validate();
            if (Input::exists()) {
                if (Token::check(Input::get('token'))) {
                    $validate->check($_POST,[
                        "name"=>[
                            "required"=>true,
                            "min"=>6,
                            "max"=>12
                        ],
                        "email"=>[
                            "required"=>true,
                            "email"=>true,
                            // "unique"=>"table_name"
                            "min"=>6,
                            "max"=>12
                        ],
                        "phone"=>[
                            "required"=>true,
                            "number"=>true,
                            "min"=>10,
                            "max"=>12
                        ]
                    ]);
                    $view = new View('register');
                    if($validate->passed()){
                        //do operation
                        $salt=Hash::salt(32);
                        $password=Hash::make(Input::get('password'),$salt);
                        $register=[
                            "name"=>Input::get('name'),
                            "phone"=>Input::get('phone'),
                            "email"=>Input::get('email'),
                            "salt"=>$salt,
                            "password"=>$password
                        ];
                        array_push($this->list,$register);

                        $view->assign("result",$validate->errors());
                    }else{                        
                        $view->assign("error",$validate->errors());
                    }
                } else {
                    var_dump("error token");
                }
            } else {
                Redirect::to(WEB_ROOT);
            }
        }

        function addList($req){
            var_dump($req);
            // Redirect::to("/");
        }

    }
?>