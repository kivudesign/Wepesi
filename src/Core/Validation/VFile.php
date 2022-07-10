<?php

namespace Wepesi\Core\Validation;

class VFile extends ABIValidation
{
    private string $file_name;
    private array $source_data;
    private object $lang;

    function __construct(array $source,string $file){
        $this->lang= (object)LANG_VALIDATE;
        $this->file_name=$file;
        $this->source_data=$source;
        if(!isset($this->source_data[$file])){
            return $this->check_file_existe();
        }
    }
    /**
     * @return bool
     */
    private function check_file_existe(){
        if (!isset($this->source_data[$this->file_name])) {
            $message = [
                "type"=> "any.unknown",
                "message" => "`{$this->file_name}` is uknown",
                "label" => $this->file_name,
            ];
            $this->addError($message);
        }
        return true;
    }
    function required(){
        if (count($this->source_data)==0 || !isset($this->source_data[$this->file_name])) {
            $message = [
                "type"=> "number.required",
                "message" => "`{$this->file_name}` is required",
                "label" => $this->file_name
            ];
            $this->addError($message);
        }
        return $this;
    }

    function min()
    {
        // TODO: Implement min() method.
    }

    function max()
    {
        // TODO: Implement max() method.
    }
}