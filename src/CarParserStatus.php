<?php
class CarParserStatus
{
    //represents the status of an attempt to create a car and the status of an attempt to insert a days worth of case reviews into the database.
    
    public $selectedUrl;
    public $date;
    public $message;
    public $runTime;
    public $statusCode;

    public function __construct(){}

    //Setters
    public function setRuntime($secs){
        $bit = array(
            ' Years' => $secs / 31556926 % 12,
            ' Weeks' => $secs / 604800 % 52,
            ' Days' => $secs / 86400 % 7,
            ' Hours' => $secs / 3600 % 24,
            ' Minutes' => $secs / 60 % 60,
            ' Seconds' => $secs % 60
            );
            
        foreach($bit as $k => $v)
            if($v > 0)$ret[] = $v . $k;
    
            if($ret === null){
                return 0 . " Seconds";
            }
    
        $this->runtime =  implode(' ',$ret);
    }

    public function setDate($date){
        $this->date = $date->format("n/j/Y");
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function setUrl($url){
        $this->selectedUrl = $url;
    }

    public function setStatusCode($statusCode){
        $this->statusCode = $statusCode;
    }

    //Getters
    public function getDate(){
        return $this->date;
    }

    public function getRuntime(){
        return $this->runtime;
    }

    public function getMessage(){
        return $this->message;
    }

    public function getStatusCode(){
        return $this->statusCode;
    }

    public function getSelectedUrl(){
        return $this->selectedUrl;
    }
}