<?php

namespace Wepesi\App\Core;

class DBCreateMultiple
{
    private $table, $_pdo;
    private $_fields;
    private $_error,$_lastid;

    function __construct(\PDO $pdo, string $table)
    {
        $this->table = $table;
        $this->_pdo = $pdo;
    }
    //
    function field(array $fields = [])
    {
        try{
            if (count($fields) ) {
                $myKeysFields=[];
                if(isset($fields[0]) && is_array($fields[0])){
                    foreach ($fields[0] as $field=>$value){
                        array_push($myKeysFields, trim($field));
                    }
                    $prepared_field_keys ="(". implode(',', $myKeysFields).")" ;
                    $expected_prepared_params="";
                    $y=0;
                    $value_prepared_params=[];
                    foreach ($fields as $elements){
                        $prepared_params="";
                        $x=0;
                        foreach ($elements as $element=>$field){
                            array_push($value_prepared_params,$field);
                            $prepared_params .= "? ";
                            if ($x < count($elements)-1) {
                                $prepared_params .= ', ';
                            }
                            $x++;
                        }
                        $expected_prepared_params.="($prepared_params)";
                        if($y<count($fields)-1){
                            $expected_prepared_params.=",";
                        }
                        $y++;
                    }
                    $this->_fields = [
                        "prepared_field_key" => $prepared_field_keys,
                        "prepare_params" => $expected_prepared_params,
                        "prepare_value_params" => $value_prepared_params
                    ];
                }else{
                    echo "format data is not correct";
                }
            }
        }catch (\Exception $ex){
            echo $ex->getMessage();
        }
        return $this;
    }

    /**
     * @param $sql
     * @param array $params
     * @return $this
     * this module is use to execute sql request
     */
    private function query($sql, array $params = [])
    {
        $q = new DBQeury($this->_pdo, $sql, $params);
        $this->_error = $q->getError();
        $this->_lastid = $q->lastId();
        return $this;
    }

    /**
     * @return bool
     * use this module to create new record
     */
    private function insert()
    {
        if (isset($this->_fields['keys']) && isset($this->_fields['values']) && isset($this->_fields['params'])) {
            $fields = $this->_fields['prepared_field_key'];
            $prepared_values = $this->_fields['prepare_params'];
            $params_values = $this->_fields['prepare_value_params'];
            $sql = "INSERT INTO $this->table $fields VALUES $prepared_values";
            var_dump($sql);
            if (!$this->query($sql, $params_values)->error()) {
                return true;
            }
        }
        return false;
    }
    /**
     * @return bool
     * return result after a request select
     */
    function result()
    {
        $this->insert();
        return $this->_lastid;
    }
    // return an error status when an error occure while doing an querry
    function error()
    {
        return $this->_error;
    }
    /**
     * @return mixed
     * access the last id record after creating a new record
     */
    function lastId()
    {
        return $this->_lastid;
    }
}