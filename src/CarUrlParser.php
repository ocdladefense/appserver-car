<?php
class CarUrlParser{
    
    // private $URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";
    const BASE_URL = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews";
    const COURTS = array("Oregon_Appellate_Court",
                        "Oregon_Supreme_Court",
                        "U.S._Supreme_Court",
                        "Oregon_Appellate_Ct",
                        "Oregon_Supreme_Ct",
                        "U.S._Supreme_Ct",
                        "Oregon_Court_of_Appeals");
    const COURT_URL_DATE_SEPERATOR_1 = ",_";
    const COURT_URL_DATE_SEPERATOR_2 = "_";
    const COURT_URL_DATE_SEPERATOR_3 = "_-_";
    const COURT_URL_DATE_SEPERATOR_4 = "--";


    private $protocol;
    private $domain;
    private $path;
    private $carUrl = array();
    private $url;
    private $selectedUrl;
    private $validResponse;
    private $stringDates;
    private $urlPreference; //define the pattern for each set of date ranges
    private $outputObjs = array();
    private static $usePreferredUrls = true;
    private $maxUrlTests = 0;


    function __construct($date){

        //print("date".$date);exit;

        $this->stringDates = array($date->format("F_j,_Y"),$date->format("M_j,_Y"),$date->format("n-j-y"),$date->format("m-j-y"));

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

    function patchSeptemberMonthName($dateString){
        //Sep_7,_2017
        $dateParts = explode("_",$dateString);
        $month = $dateParts[0];

        if($month == "Sep"){
            $dateParts[0] = "Sept";
        }
        return implode("_",$dateParts);
    }

    function needsSeptemberPatch($dateString){
        $dateParts = explode("_",$dateString);
        return $dateParts[0] == "Sep";
    }
    
    function candidateUrls(){
        $urls = array();
        $variations = array();

        $urls = array_map(function($court){
           return self::BASE_URL."/".$court;
        },self::COURTS);

        foreach($this->stringDates as $format){
            foreach($urls as $url){
                $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_1.$format;
                $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_2.$format;
                $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_3.$format;
                $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_4.$format;
                if($this->needsSeptemberPatch($format)){
                    $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_1.$this->patchSeptemberMonthName($format);
                    $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_2.$this->patchSeptemberMonthName($format);
                    $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_3.$this->patchSeptemberMonthName($format);
                    $variations[] = $url.self::COURT_URL_DATE_SEPERATOR_4.$this->patchSeptemberMonthName($format);
                }
            }
        }
        return $variations;
    }

    function makeRequests(){
        $candidateUrls = $this->candidateUrls();
        $iteration = 0;
        $preferredUrls = $this->getPreferredUrls();
        $time = time();

        //give preference to preferred urls to reduce execution time.
        $allUrls = self::$usePreferredUrls === true ? array_merge($preferredUrls,$candidateUrls) : $candidateUrls;


        

        foreach($allUrls as $url){
            $iteration++;
            if($iteration > $this->maxUrlTests) break;
            $req = new HttpRequest($url);
            $resp = $req->send();
            //$this->displayOutput($url,$resp,$iteration);
            $this->outputObjs[] = $this->setOutput($url,$resp);

        
            if($resp->getStatusCode() == 200){
                //$this->displayOutput($url,$resp,$iteration);
                $this->outputObjs[] = $this->setOutput($url,$resp);
                $this->selectedUrl = $url;
                $this->validResponse = $resp;
                return $resp;
                break;
            }
        }
        //if no candidate urls return null;
        return null;
    }

    function getPreferredUrls(){
        //returns a url 
        $preferred = array(
            self::BASE_URL."/".self::COURTS[0].self::COURT_URL_DATE_SEPERATOR_1.$this->stringDates[0],
            self::BASE_URL."/".self::COURTS[1].self::COURT_URL_DATE_SEPERATOR_1.$this->stringDates[0],
            self::BASE_URL."/".self::COURTS[0].self::COURT_URL_DATE_SEPERATOR_3.$this->stringDates[0],
            self::BASE_URL."/".self::COURTS[1].self::COURT_URL_DATE_SEPERATOR_3.$this->stringDates[0],
            self::BASE_URL."/".self::COURTS[0].self::COURT_URL_DATE_SEPERATOR_4.$this->stringDates[0],
            self::BASE_URL."/".self::COURTS[1].self::COURT_URL_DATE_SEPERATOR_4.$this->stringDates[0]
        );

        return $preferred;
    }


    //Testing functions

    function toUrl(){

        //$url = self::BASE_URL.self::COURT[0].self::COURT_DATE_SEPERATOR.$this->stringDate;
        $urls = $this->candidateUrls();
        print("<pre>".print_r($urls,true)."</pre>");
        return $urls[0];
    }

    function displayOutput($url,$resp,$iteration){
        print("<br><strong>#".$iteration." The given url '".$url."' returned a status code of ".$resp->getStatusCode()."</strong><br>");
        //change the name of the function
    }

    function setOutput($url,$resp){
        $output = new StdClass();
        $output->url = $url;
        $output->statusCode = $resp->getStatusCode();

        return $output;
    }

    function getOutput(){
        return $this->outputObjs;
    }

    function getUrls(){
        $candidateUrls = $this->candidateUrls();
        $iteration = 0;

        $preferredUrls = $this->getPreferredUrls();

        return array_merge($preferredUrls,$candidateUrls);
    }

    public function getDocumentParser(){
        //Pass the body of the page to the DocumentParser
        if($this->validResponse != null){
            $page = new DocumentParser($this->validResponse->getBody());
            //We are only concerned with the content located in the 'mw-content-text' class of the page
            $fragment = $page->fromTarget("mw-content-text");

            return $fragment;
        }
    }
    public function getSelectedUrl(){
        return $this->selectedUrl;
    }

    public function setMaxUrlTests($number){
        $this->maxUrlTests = $number;
    }
}