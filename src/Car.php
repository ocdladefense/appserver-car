<?php
class Car{
    public $subjects;
    public $summary;
    public $result;
    public $case;
    public $citation;
    public $decisionDate;
    public $circutCourt;
    public $circutCourtJudge;
    public $plaintiff;
    public $defendant;
    public $linkNode;
    public $subNode;
    public $citationNode;

    public function __construct($subNode,$linkNode){
        $this->subNode = $subNode;
        $this->linkNode = $linkNode;
        $this->citationNode = $this->linkNode->nextSibling;
    }

    function parse(){
        $this->subjects = explode(" - ",$this->subNode->nodeValue);
        $this->summary = $this->getSummary($this->subNode);
        $this->result = $this->getCaseResult($this->summary);
        $this->case = $linkNode->nodeValue;
        list($this->plaintiff,$versus,$this->defendant) = explode(" ",$this->case);
        $this->citations = $this->toArray($this->citationNode);
        list($this->month,
            $this->day,
            $this->year,
            $this->judge,
            $this->county1,
            $this->county2,
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
        return preg_split("/[()\s,\n]+/",$node->nodeValue);
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