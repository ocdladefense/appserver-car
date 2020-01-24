<?php
class CarUrlParser{
    
    // private $URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";
    private $URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_";

    private $protocol;
    private $domain;
    private $path;
    private $carUrl = array();
    private $url;
    private $month;
    private $monthName;
    private $day;
    private $year;
    private $datetime;


    function __construct($date){

        $this->datetime = $date;
        list($this->protocol,$this->url) = explode("//",$this->URL_TO_PAGE);

        list($this->domain,$this->path,$this->carUrl) = explode("/",$this->url);
    }

    function getProtocol(){
        return $this->protocol;
    }

    function getDomain(){
        return $this->domain;
    }

    function getPath(){
        return $this->path;
    }

    function parseUrlDate(){
        $obj = new ReflectionObject($this->datetime);
        $prop = $obj->getProperty('date');
        $date = $prop->getValue($this->datetime);
        $datePieces = preg_split("/[-\s]/",$date);
        list($this->year,$this->month,$this->date,$timeStuff) = $datePieces;

        $dateObj   = DateTime::createFromFormat('!m', $this->month);
        $this->monthName = $dateObj->format('F');
    }

    function toUrl(){
        $this->parseUrlDate();

        $url = $this->URL_TO_PAGE.$this->monthName."_".$this->date.",_".$this->year;
        return $url;
    }
}