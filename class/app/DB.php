<?php
    class DB
    {
        private static $_instance = null;
        private $_pdo,
            $_query,
            $_error,
            $_results = false,
            $_lastid,
            $_count = 0;

        private function __construct()
        {
            try {
                $this->_pdo = new PDO("mysql:host=" . Config::get('mysql/host') . ";dbname=" . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
            } catch (PDOException $ex) {
                die($ex->getMessage());
            }
        }
        static function getInstance(){
            if(!isset(self::$_instance)){
                self::$_instance=new DB();
            }
            return self::$_instance;
        }
        function query($sql,$params=[]){
            $this->_error=false;
            if($this->_query=$this->_pdo->prepare($sql)){
                $x=1;
                if(count($params)){
                    foreach($params as $param){
                        $this->_query->bindValue($x,$param);
                        $x++;
                    }
                }
                if($this->_query->execute()){
                    $this->_results=$this->_query->fetchAll(PDO::FETCH_OBJ);
                    $this->_count=$this->_query->rowCount();                    
                }else{
                    $this->_error=true;
                }
            }
            return $this;
        }
        function insert($table,$fields=[]){
            if(count($fields)){
                $keys=array_keys($fields);
                $values=null;
                $x=1;
                foreach($fields as $field){
                    $values.="?, ";
                    if($x<count($fields)){
                        $values.=', ';
                    }
                    $x++;
                }
                $sql="INSERT INTO {$table} ('". implode('`,`',$keys) . "') VALUES ({$values})";
                if(!$this->query($sql,$fields)){
                    $this->_lastid = $this->query->lastInsertId();
                    return true;
                }
            }
            return false;
        }
        function update($table,$where, $fields){
            $set='';
            $id='';
            $x=1;
            $y=1;
            // manage fields to be where updated
            foreach($fields as $name=>$value){
                $set.="{$name}=?";
                if($x<count($fields)){
                    $set.=", ";
                }
                $x++;
            }
            // manage where condition
            foreach($where as $name=>$value){
                $id.="{$name}=?";
                if($y<count($where)){
                    $id.=" AND ";
                }
                $y++;
            }
            $fields=array_merge($fields,$where);
            $sql="UPDATE {$table} SET {$set} WHERE {$id}";

            if(!$this->query($sql,$fields)->error()){
                return true;
            }
            return false;
        }
        private function action($action,$table,$where=[]){
            if(count($where)){
                $operators=array('=','>','<','>=','<=','<>','!=');

                $field=$where[0];
                $operator=$where[1];
                $value=$where[2];
                if(in_array($operator,$operators)){
                    $sql="{$action} FROM {$table} WHERE {$field}{$operator} ?";
                    if(!$this->query($sql,array($value))->error()){
                        return $this;
                    }
                }
            }
        }
        
        function get($table,$where=[],$fields=[],$order=null){
            $field="*";            
            if(count($fields)>0 && is_array($fields)){
                $field="";
                $i=1;
                foreach($fields as $name){
                    $field.= $name;
                    if($i<count($fields)){
                        $field.=",";
                    }
                    $i++;
                }
            }
            if(!count($where)){
                $sql = "SELECT {$field} FROM {$table}";  
                if($order!=null && strlen($order)>5){
                    $sql.=" {$order}";
                }   
                return $this->query($sql,[]);
            }else{
                return $this->action("SELECT {$field} ",$table,$where);
            }           
        }
        function delete($table,$where){
            return $this->action('DELETE', $table, $where);
        }

        function error(){
            return $this->_error;
        }

        function count(){
            return $this->_count;
        }

        function result(){
            return $this->_results;
        }
        function lastId(){
            return $this->_lastid;
        }

        function join($tables,$rules,$on,$where){
            
            $sql="SELECT ";
            // $tables=
            //     [
            //         "users"=>["sexe", "birthday", "phonenumber", "email", "username"],
            //         "account"=>["grade", "company", "about", "city", "country", "adress"],
            //         "usersdomaine"=>["designation"],
            //         "media"=>["link as avatar"],
            //         "userlevel"=>["designation as level"]
            //     ];
            // $rules=
            //         [
            //             "users"=>["accounts","Left Join"],
            //             "accounts"=>["usersdomaine","Left Join"],
            //             "usersdomaine"=>["media","Left Join"],
            //             "media"=>["userlevel","Join"]                    
            //         ];
            //the rule of the array, start with the table to join from after table to join after 
            // e.g: for=>user.iduser=account.idacount  <=> ["users","account","iduser","idacount"]
            // $on=
            //         [
            //             ["users","accounts","iduser", "iduser"],
            //             ["accounts","usersdomaine","iddomain", "iddomain"],
            //             ["users","media","idavatar", "idmedia"],
            //             ["users","userlevel","iduserlevel", "iduserlevel"]                    
            //         ];
            
            if(count($tables)){
                $i=0;
                $SelectedField="";
                
                $tlen= 0;
                foreach($tables as $tableName=>$tableValues){
                    $tlen+=count($tableValues);

                    foreach($tableValues as $tableValue){

                        $SelectedField.="{$tableName}.{$tableValue}";
                        
                        if($i<$tlen){
                            $SelectedField.=",";
                        }                        
                        $i++;   
                    }
                }
                $SelectedField= substr_replace($SelectedField, "", (strlen($SelectedField) - 1));
                //
                $selectedTables="";
                $j=0;
                $rlen=0;
                $sql.=$SelectedField;
                if(count($rules)){
                    foreach($rules as $tablesRules=>$tableRulevalues){
                        //table name concat the field on the table
                        $tableToJoin = $on[$j][0].".". $on[$j][2]; 
                        $tableToBeJoin = $on[$j][1].".". $on[$j][3];
                        
                        $rlen+=count($tableRulevalues);
                        $selectedTables.="{$tablesRules} {$tableRulevalues[1]} {$tableRulevalues[0]} ON {$tableToJoin}={$tableToBeJoin} ";                       
                        $j++;
                    }
                    // $this->action($sql,$selectedTables,$where);
                    var_dump($sql);
                }
            }
        }
    }
?>