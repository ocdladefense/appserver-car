<?php
class CarUrlParser{
    private $url = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";

    //private static whatever

    public $protocol;
    public $domain;
    public $path;
    public $theRest = array();

    function __construct($url){
    
        list($this->protocol,$this->url) = explode("//",$url);

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

    function parseUrl(){
        $lastParts = explode("/",$this->url);
//   $data = preg_split("/[_,]+/",$this->carUrl)
/// $data[0] = "
        return implode($lastPart,"_");
    }

    // function fromDate($date){
    //     $parser = new CarUrlParser();
    //     $parser->month = ; $parser->day = ; $parser->year = "2019";

    //     return $parser;
    // }

    function toUrl(){
        $url = self::proto . "//" . sefl::domain . "/" . self::path;
        $url .= "{$this->month}_{$this->day},_{$this->year}";
        return $url;
    }
}