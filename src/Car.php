<?php
class Car{
    const CITATION_INDEX = 0;

    const DATE_INDEX = 1;

    const MONTH_INDEX = 0;

    const DAY_INDEX = 1;

    const YEAR_INDEX = 2;

    const MAJORITY_INDEX = 2;

    const CIRCUT_AND_JUDGES_INDEX = 3;

    const URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_";

    public $id;
    public $title;
    public $subject_1;
    public $subject_2;
    public $summary;
    public $result;
    public $plaintiff;
    public $defendant;
    public $citation;
    public $month;
    public $day;
    public $year;
    public $majority;
    public $circut;
    public $judges;
    public $url;

    private $subjectNode;

    private $linkNode;

    private $firstParagraphElement;

    private $citationNodeValue;

    private $citationNodeValueParts;

    private $subjects;


    public function __construct($subjectNode,$linkNode){
        $this->subjectNode = $subjectNode;
        $this->linkNode = $linkNode;
        //the name of the firstParagraphElement prop needs to change.  Maybe subjectNodeParent or ....
        $this->firstParagraphElement = $this->setFirstParagraphElement($this->subjectNode->parentNode);
        $this->citationNodeValue = $this->linkNode->nextSibling->nodeValue;
        $this->citationNodeValueParts = $this->citationToArray($this->citationNodeValue);
    }

    function parse(){
        if($this->subjectNode == null){
            throw new CarParserException("The subject node cannot be null");
        }

        if($this->linkNode == null){
            throw new CarParserException("The link node cannot be null");
        }

        // if($this->subjectNode->parentNode->nodeName != "p"){
        //     throw new CarParserException("parent is not a p element");
        // }

        $this->subjects = $this->getSubjects($this->subjectNode->nodeValue);

        $this->subject_1 = $this->subjects[0];
        $this->subject_2 = $this->subjects[1];
        $this->summary = $this->setSummary();   

        if($this->summary == null){
            throw new CarParserException("The summary cannot be null");
        }

        $this->result = $this->setCaseResult($this->summary);
        if($this->linkNode == null){
            throw new CarParserException("The link node cannot be null");
        }

        $this->title = $this->linkNode->nodeValue;
        if($this->title == null){
            throw new CarParserException("The title cannot be null");
        }

        list($this->plaintiff,$versus,$this->defendant) = explode(" ",$this->title);

        $this->citation = $this->citationNodeValueParts[self::CITATION_INDEX];

        $this->month = $this->getDecisionDate()[self::MONTH_INDEX];

        $this->day = $this->getDecisionDate()[self::DAY_INDEX];

        $this->year = $this->getDecisionYear();

        $this->majority = $this->getJudge();

        $this->circut = $this->getCircutCourt();

        $this->judges = $this->getOtherJudges();

        $this->url = self::URL_TO_PAGE.$this->month."_".$this->day.",_".$this->year;
    }

    //---GETTERS---
    function getSubjects($nodeValue){
        $nodeValueParts = explode("-",$this->subjectNode->nodeValue);

        foreach($nodeValueParts as $part){
            if($part !== "" && $part !== "-"){
                $this->subjects[] = $part;
            }
        }
        return $this->subjects;
    }

    function getSummary(){
        return $this->summary;
    }

    function getCaseResult(){
        return $this->result;
    }

    function getCaseTitle(){
        return $this->title;
    }

    function getLitigants(){
        return array($this->plaintiff,$this->defendant);
    }

    function getCitation(){
        return $this->citationNodeValueParts[self::CITATION_INDEX];
    }

    function getDecisionYear(){
        $year = $this->getDecisionDate()[self::YEAR_INDEX];
        if(substr($year,-1) == ")"){
            $year = rtrim($year,")");
        }
        return $year;
    }

    function getDecisionDate(){
        //return a usable date array
        $dateArray = substr($this->citationNodeValueParts[self::DATE_INDEX],0,-2);
        $dateArray = preg_split("/[\s,]+/",$dateArray);
        return $dateArray;
    }

    function getCircutCourt(){
        $circut = explode(",",$this->citationNodeValueParts[self::CIRCUT_AND_JUDGES_INDEX])[0];
        return $circut;
    }

    function getJudge(){
        return substr($this->citationNodeValueParts[self::MAJORITY_INDEX],0,-2);
    }

    function getOtherJudges(){
        $judges = explode(" ",substr(explode(",",$this->citationNodeValueParts[self::CIRCUT_AND_JUDGES_INDEX])[1],0,-2));
        if($judges[0] == ""){
            array_shift($judges);
            $judges = implode(", ",$judges);
        }
        return $judges;
    }

    //---SETTERS---
    function setSummary(){
        $summaryNodes = array();
        $summary = "";
        $count = 0;
    
        while(++$count < 10){
            $next = $this->firstParagraphElement->nextSibling;
            $this->firstParagraphElement = $next;
            if($next->nodeType == XML_TEXT_NODE) continue;
            if($next->firstChild->nodeName == "a") break;
            $summaryNodes[] = $next->nodeValue;
        }
        return implode("\n",$summaryNodes);
    }

    function setFirstParagraphElement($parentNode){
        if($parentNode->tagName !== "p"){
            $this->firstParagraphElement = $parentNode->parentNode;
        }
        else{
            $this->firstParagraphElement = $parentNode;
        }
        return $this->firstParagraphElement;
    }
    
    function setCaseResult($summaryString){
        $SENTENCE_DELIMITER = ".";
        $sentences = explode($SENTENCE_DELIMITER, $summaryString);
        $result = $sentences[count($sentences)-2];

        return $result;
    }
    function citationToArray($citationString){
        $parts = preg_split("/(\)\s*\(*)|\(/",$citationString);
        $trimmedParts = array_map(function($item){
            return trim($item);
        },$parts);

        $trimmedParts = array_filter($trimmedParts,function($item){
            return !empty($item);
        });
        //var_dump($trimmedParts);exit;
        //return explode("(",$nodeValue);
    }
}