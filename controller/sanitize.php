<?php
    function escape($string){
        return htmlentities($string,ENT_QUOTES,'UTF-8');
    }

    function timePub($timepost){
        // date_default_timezone_set(TimeZone);
        $date=date("Y-m-d H:i:s",time());
        $currentTime=strtotime($date);
     
        $oldTime=strtotime($timepost);
        $diffTime=$currentTime-$oldTime;
        $sec=$diffTime;
        $min=floor($diffTime/(60));
        $hr=floor($diffTime/(60*60));
        $day=floor($diffTime/(60*60*24));

        $time=null;
        if($day>5){
            $time = strtotime($timepost);            
            $time = date("d/m/Y", $time);
        }else{
            if($hr > 23 && $day<5){
                $time=$day."j";
            }
            else{
                if($hr>0 && $hr<24){
                    $time=$hr."h";
                }else{
                    if($min>0 && $min<60){
                        $time=$min."min";
                    }else{
                        $time=$sec."s";
                    }
                }
            }
        }        
        return $time;
    }
    
    function getMonth(string $format, int $month){
        $myMonth=["fr"=>['Janvier','Février','Mars','Avril','Maie','Juin','Juillet','Aôut','Septembre','Octobre','Novembre','Decembre']
        //you can add your format for according to the language
        ];
        return $myMonth[$format][$month];
    }
    function getDay(string $format,int $day){
        $myDay=[
            "fr"=> ['Dimanche', 'Lundi', 'mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
            //you can add your format for according to the language
        ];        
        return $myDay[$format][$day];
    }