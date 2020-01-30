<?php
class Car{

    private $subjectNode;

    private $linkNode;

    private $firstParagraph;

    private $subjects;

    private $summary;

    private $result;
    
    private $citationNodeValue;



    public function __construct($subjectNode,$linkNode){
        $this->subjectNode = $subjectNode;
        $this->linkNode = $linkNode;
        $this->firstParagraph = $this->subjectNode->parentNode;
        $this->citationNodeValue = $this->linkNode->nextSibling->nodeValue;
        $this->citationNodeValueParts = $this->toArray($this->citationNodeValue);
        //var_dump($this->citationNodeValueParts);exit;
    }

    function parse(){
        //check every value for null or " " throw exception with plenty of info in the exception message
        if($this->subjectNode == null){
            throw new CarParserException("The subject node cannot be null");
        }

        if($this->linkNode == null){
            throw new CarParserException("The link node cannot be null");
        }

        if($this->subjectNode->parentNode->nodeName != "p"){
            throw new CarParserException("parent is not a p element");
        }

        $this->subjects = explode(" - ",$this->subjectNode->nodeValue);
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


    }
    
    //---GETTERS---
    function getSubjects(){
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
        return $this->citationNodeValueParts[0];
    }

    function getDecisionDate(){
        return substr($this->citationNodeValueParts[1],0,-2);
    }

    function getCircutCourt(){
        return explode(",",$this->citationNodeValueParts[3])[0];
    }

    function getJudge(){
        return substr($this->citationNodeValueParts[2],0,-2);
    }

    function getOtherJudges(){
        return substr(explode(",",$this->citationNodeValueParts[3])[1],0,-2);
    }

    //---SETTERS---
    function setSummary(){
        $summaryNodes = array();
        $summary = "";
        // $parent = $subjectNode->parentNode;
        $count = 0;
    
        while(++$count < 10){// && null != ($next = $parent->nextSibling)){
            $next = $this->firstParagraph->nextSibling;
            $this->firstParagraph = $next;
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
    function toArray($nodeValue){
        // return preg_split("/[()\s,\r]+/m",$nodeValue);
        //return preg_split("/[(),]+/m",$nodeValue);
        return explode("(",$nodeValue);
    }
    
    // function getCircutCourt(){
    //     $citationStringParts = $this->parseCitationString();
    //     $circutCourt = $citationStringParts[9]." ".$citationStringParts[10].", ".$citationStringParts[11];
    //     return $circutCourt;
    // }
    // function getJudge(){
    //     $judge = $this->parseCitationString()[8];
    //     return $judge;
    // }


}
// $tempArray = $this->toArray($this->citationNode);
// if($tempArray == null){
//     throw new CarParserException("The tempArray holding the citation parts cannot be null");
// }
// $this->citations = array_filter($tempArray,function($elem){return $elem != "";});

// list($this->emptyElement,
//     $this->cit1,
//     $this->cit2,
//     $this->cit3,
//     $this->cit4,
//     $this->month,
//     $this->day,
//     $this->year,
//     $this->judge,
//     $this->countyPart1,
//     $this->countyPart2,
//     $this->countyPart2,
//     $this->countyPart4,
//     //$this->countyPart5,
//     $this->judge2) = $this->citations;