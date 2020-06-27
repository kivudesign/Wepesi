<?php

    class BoxMessage{
        static function errors($type,$value){
            $message=array();
            $err_msg=null;
            if($value){
                $err_msg="<p class='w3-text-red'>".$value."</p>";
            }
            switch($type){
                case 1: $message=array( 'type'=>'Error Input',
                                        'errorText'=>'there is a field fitch is empty');
                break;
                case 2: $message=array( 'type'=>'No Information',
                                        'errorText'=>'your infos is not correct, try again');
                break;
                case 3: $message=array( 'type'=>'Error Operation',
                                        'errorText'=>'error while try to save'.$err_msg);
                break;
                case 4: $message=array( 'type'=>'Error Operation',
                                        'errorText'=>'error while try to update'.$err_msg);
                break;
                case 5: $message=array( 'type'=>'Error Operation',
                                        'errorText'=>'error while try to delete'.$err_msg);
                break;
                case 6: $message=array( 'type'=>'Error Operation',
                                        'errorText'=>'No data found'.$err_msg);
                break;
                case 123: $message=array( 'type'=>'Page Error',
                                        'errorText'=>'Opation impossible');
                break;
                case 10: $message=array( 'type'=>'error param',
                                        'errorText'=>'information is not correct');
                break;
                case 11: $message=array( 'type'=>'error data',
                                        'errorText'=>'error on datatype');
                break;
            }        
            return $message;
        }

        static function success($val){
            $message=array();
            switch($val){
                case 1: $message=array('type'=>'success Save','text'=>'Register with success');
                break; 
                case 2: $message=array('type'=>'success Update','text'=>'Update with success');
                break; 
                case 3: $message=array('type'=>'success Delete','text'=>'Delete with success');
                break; 
                case 4: $message=array('type'=>'found','text'=>'Result found');
                break; 
                case 5: $message=array('type'=>'Success','text'=>'Operation Success');
                break; 
            }
            return $message;        
        }
        static function warning($val){
            $message=array();
            switch($val){
                case 1: $message=array('type'=>'Record','warning'=>'There is no record found');
                break; 
                case 2: $message=array('type'=>'Data','warning'=>'No data found');
                break; 
                case 3: $message=array('type'=>'Param','warning'=>'information is not correct');
                break; 
                case 4: $message=array('type'=>'found','warning'=>'');
                break; 
            }
            return $message;        
        }
    }
?>