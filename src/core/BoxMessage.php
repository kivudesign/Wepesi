<?php

namespace Wepesi\App\Core;

    class BoxMessage{
        private static $lang;
        function __construct()
        {
            self::$lang=(object)LANG_BOX_MESSAGE;
        }
        /**
         * 
         * @param type $type : this define what is the type of message we are going to deal with
         * @param type $result : the message text to display, or group of message (array)
         * @return array string
         */
        static function errors($type,$result){
            $message=[];
            $err_msg=null;
            if(is_array($result) ){
                $err_msg=$result;
            }elseif($result){
                $err_msg[]=$result;                
            }

            switch($type){
                case 1: $message=[ 'type'=>'Error Input',
                                        'errorText'=>$err_msg];
                break;
                case 2: $message=[ 'type'=>'No Information',
                                        'errorText'=>self::$lang->no_information];
                break;
                case 3: $message=[ 'type'=>'Error Operation Save',
                                        'errorText'=> $err_msg? $err_msg:self::$lang->error_operation_save];
                break;
                case 4: $message=[ 'type'=>'Error Operation Update',
                                        'errorText'=>$err_msg? $err_msg:self::$lang->error_operation_update];
                break;
                case 5: $message=[ 'type'=>'Error Operation delete',
                                        'errorText'=>$err_msg? $err_msg:self::$lang->error_operation_delete];
                break;
                case 6: $message=[ 'type'=>'No Data ',
                                        'errorText'=>$err_msg? $err_msg:self::$lang->no_data];
                break;
                case 123: $message=[ 'type'=>'Page Error',
                                        'errorText'=>$err_msg? $err_msg:self::$lang->page_error];
                break;
                case 10: $message=[ 'type'=>'error param',
                                        'errorText'=>$err_msg? $err_msg:self::$lang->error_param];
                break;
                case 11: $message=[ 'type'=>'error data',
                                        'errorText'=>$err_msg? $err_msg:self::$lang->error_data];
                break;
            }        
            return $message;
        }

        static function success($val){
            $message=array();
            switch($val){
                case 1: $message=['type'=>'success Save','text'=>self::$lang->success_save];
                break; 
                case 2: $message=['type'=>'success Update','text'=>self::$lang->success_update];
                break; 
                case 3: $message=['type'=>'success Delete','text'=>self::$lang->success_delete];
                break; 
                case 4: $message=['type'=>'found','text'=>self::$lang->found];
                break; 
                case 5: $message=['type'=>'Success','text'=>self::$lang->success_operation];
                break;
                default:
                    $message=['type'=>'Success','text'=>$val];
                    break;
            }
            return $message;        
        }
        static function warning($type){
            $message=array();
            switch($type){
                case 1: $message=['type'=>'Warning Record','warning_text'=>'There is no record found'];
                break; 
                case 2: $message=['type'=>'Warning Data','warning_text'=>'No data found'];
                break; 
                case 3: $message=['type'=>'Not Data','warning_text'=>'information is not correct'];
                break; 
                case 4: $message=['type'=>'Warning found','warning_text'=>''];
                break;
                default:
                    $message=['type'=>'Warning found','warning_text'=>$type];
                    break;
            }
            return $message;        
        }
    }
