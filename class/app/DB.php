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
                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
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
                $sql="INSERT INTO {$table} (`". implode('`,`',$keys) . "`) VALUES ({$values})";
                if(!$this->query($sql,$fields)->error()){
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
            $params=[];
            // manage fields to be where updated
            foreach($fields as $name=>$value){
                $set.="{$name}=?";
                array_push($params,$value);
                if($x<count($fields)){
                    $set.=", ";
                }
                $x++;
            }
            // manage where condition
            foreach($where as $name=>$value){
                $id.="{$name}=?";
                array_push($params,$value);
                if($y<count($where)){
                    $id.=" AND ";
                }
                $y++;
            }
            $params=array_merge($fields,$where);
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
    }
?>