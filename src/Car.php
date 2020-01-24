<?php
class Car{

    private $subjectNode;

    private $linkNode;
    
    private $citationNode;

    private $subjects;

    private $summary;

    private $result;

    private $citations;



    public function __construct($subjectNode,$linkNode){
        $this->subjectNode = $subjectNode;
        $this->linkNode = $linkNode;
        $this->citationNode = $this->linkNode->nextSibling;
    }

    function parse(){
        $this->subjects = explode(" - ",$this->subjectNode->nodeValue);
        $this->summary = $this->getSummary($this->subjectNode);
        $this->result = $this->getCaseResult($this->summary);
        $this->case = $this->linkNode->nodeValue;
        list($this->plaintiff,$versus,$this->defendant) = explode(" ",$this->case);
        $tempArray = $this->toArray($this->citationNode);
        $this->citations = array_filter($tempArray,function($elem){return $elem != "";});
        list($this->emptyElement,
            $this->cit1,
            $this->cit2,
            $this->cit3,
            $this->cit4,
            $this->month,
            $this->day,
            $this->year,
            $this->judge,
            $this->countyPart1,
            $this->countyPart2,
            $this->judge2) = $this->citations;
    }

    function getSummary($subjectNode){
        $summaryNodes = array();
        $summary = "";
        $parent = $subjectNode->parentNode;
        $count = 0;
    
        if($parent->nodeName != "p"){
            throw new Exception("parent is not a p element");
        }
        while(++$count < 10){// && null != ($next = $parent->nextSibling)){
            $next = $parent->nextSibling;
            $parent = $next;
            if($next->nodeType == XML_TEXT_NODE) continue;
            if($next->firstChild->nodeName == "a") break;
            $summaryNodes[] = $next->nodeValue;
        }
        return implode("\n",$summaryNodes);
    }
    
    function getCaseResult($summaryString){
        $SENTENCE_DELIMITER = ".";
        $sentences = explode($SENTENCE_DELIMITER, $summaryString);
        $result = $sentences[count($sentences)-2];

        return $result;
    }

    function toArray($node){
        return preg_split("/[()\s,\r]+/m",$node->nodeValue);
    }
    
    function getDecisionDate(){
        return $this->month." ".$this->day.", ".$this->year;
    }
    
    function getCircutCourt(){
        $citationStringParts = $this->parseCitationString();
        $circutCourt = $citationStringParts[9]." ".$citationStringParts[10].", ".$citationStringParts[11];
        return $circutCourt;
    }
    function getJudge(){
        $judge = $this->parseCitationString()[8];
        return $judge;
    }

}