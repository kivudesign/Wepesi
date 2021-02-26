<?php

    class BoxMessage{
        static function errors($type,$value){
            $lang = (object)LANG_BOX_MESSAGE;
            $message=array();
            $err_msg=null;
            if($value){
                $err_msg="<p class='w3-text-red'>".$value."</p>";
            }
            switch($type){
                case 1: $message=[ 'type'=>'Error Input',
                                        'errorText'=>$err_msg];
                break;
                case 2: $message=[ 'type'=>'No Information',
                                        'errorText'=>$lang->no_information];
                break;
                case 3: $message=[ 'type'=>'Error Operation Save',
                                        'errorText'=> $err_msg? $err_msg:$lang->error_operation_save];
                break;
                case 4: $message=[ 'type'=>'Error Operation Update',
                                        'errorText'=>$err_msg? $err_msg:$lang->error_operation_update];
                break;
                case 5: $message=[ 'type'=>'Error Operation delete',
                                        'errorText'=>$err_msg? $err_msg:$lang->error_operation_delete];
                break;
                case 6: $message=[ 'type'=>'No Data ',
                                        'errorText'=>$err_msg? $err_msg:$lang->no_data];
                break;
                case 123: $message=[ 'type'=>'Page Error',
                                        'errorText'=>$err_msg? $err_msg:$lang->page_error];
                break;
                case 10: $message=[ 'type'=>'error param',
                                        'errorText'=>$err_msg? $err_msg:$lang->error_param];
                break;
                case 11: $message=[ 'type'=>'error data',
                                        'errorText'=>$err_msg? $err_msg:$lang->error_data];
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
            $lang = (object)LANG_BOX_MESSAGE;
            switch($val){
                case 1: $message=['type'=>'success Save','text'=>$lang->success_save];
                break; 
                case 2: $message=['type'=>'success Update','text'=>$lang->success_update];
                break; 
                case 3: $message=['type'=>'success Delete','text'=>$lang->success_delete];
                break; 
                case 4: $message=['type'=>'found','text'=>$lang->found];
                break; 
                case 5: $message=['type'=>'Success','text'=>$lang->success_operation];
                break; 
            }
            return $message;        
        }
    }
?>