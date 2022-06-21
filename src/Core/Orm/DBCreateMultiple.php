<?php

namespace Wepesi\Core\Orm;

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
                if(isset($fields[0]) && is_array($fields[0])){
                    $extracted_field_key= array_keys($fields[0]);

                    $prepared_field_keys ="(". implode(',', $extracted_field_key).")" ;
                    $expected_prepared_params="";
                    $y=0;
                    $value_prepared_params=[];
                    foreach ($fields as $data_elements){
                        $prepared_params="";
                        $x=0;
                        foreach ($data_elements as $element=>$field){
                            $value_prepared_params[]=$field;
                            $prepared_params .= "? ";
                            if ($x < count($data_elements)-1) {
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
        $q = new DBQuery($this->_pdo, $sql, $params);
        $this->_error = $q->getError();
        $this->_lastid = $q->lastId();
        return $this;
    }

    private function checkFieldKeyExist(array $source_data){
        $fieldKey=[];
        $source_data=(isset($source_data[0]) && is_array($source_data[0]))?$source_data:[$source_data];
        if(isset($source_data[0]) && is_array($source_data[0])){
            $fieldKey=array_keys($source_data[0]);
        }
        $extracted_key=array_keys($source_data);
        if(count($extracted_key)!=count($fieldKey)) return false;
        foreach ($fieldKey as $key){
            if(!isset($source_data[$key])) return false;
        }
        return $this->formatFieldData($fieldKey,$source_data);
    }
    private function formatFieldData(array $fieldKey,array $source_data): array
    {
        $new_formated_row=[];
        foreach ($source_data as $rows){
            $new_formated_data=[];
            foreach ($fieldKey as $key){
                $new_formated_data[$key]=$rows[$key];
            }
            $new_formated_row[]=$new_formated_data;
        }
        return $new_formated_row;
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