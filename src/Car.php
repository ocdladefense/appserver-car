<?php
class Car{
    const CITATION_INDEX = 0;

    const DATE_INDEX = 1;

    const MAJORITY_INDEX = 2;

    const CIRCUT_AND_JUDGES_INDEX = 3;
    
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


    public function __construct($subjectNode,$linkNode,$url){
        $this->subjectNode = $subjectNode;
        $this->linkNode = $linkNode;
        //the name of the firstParagraphElement prop needs to change.  Maybe subjectNodeParent or ....
        $this->firstParagraphElement = $this->subjectNode->parentNode;
        //var_dump($this->firstParagraphElement);exit;
        $this->citationNodeValue = $this->linkNode->nextSibling->nodeValue;
        $this->citationNodeValueParts = $this->citationToArray($this->citationNodeValue);
        $this->url = $url;
    }

    function parse(){
        if($this->subjectNode == null){
            throw new CarParserException("The subject node cannot be null");
        }

        if($this->linkNode == null){
            throw new CarParserException("The link node cannot be null");
        }

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

        list($this->month, $this->day, $this->year) = $this->getDecisionDate();

        $this->majority = $this->getJudge();

        $this->circut = $this->getCircutCourt();

        $this->judges = $this->getOtherJudges();
    }

    //---GETTERS---
    function getSubjects($nodeValue){
        $parts = preg_split("/—|\s+-\s+|-{2}/",$nodeValue);
        $subs = array();

        foreach($parts as $part){
            if(strlen($part) > 2 ){
                $subs[] = $part;
            }
        }
        return $subs;
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

    function getDecisionDate(){
        //return a usable date array
        $dateArray = $this->citationNodeValueParts[self::DATE_INDEX];
        $dateArray = preg_split("/[\s,]+/",$dateArray);

        return $dateArray;
    }

    function getCircutCourt(){
        $circut = explode(",",$this->citationNodeValueParts[self::CIRCUT_AND_JUDGES_INDEX])[0];
        if($circut == null){
            return "No circut info listed";
        }
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
        if($judges == null){
            return "No judges listed";
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

        return $trimmedParts;
    }
}