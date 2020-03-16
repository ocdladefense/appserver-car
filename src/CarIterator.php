<?php
class CarIterator implements Iterator{
    
    private $nodeList;

    private $startAt = 0;

    private $counter = 0;

    private $testFunc;

    private $log = array();


    public function __construct($nodeList){
        $this->nodeList = $nodeList;
    }

    public function current(){
        $node = $this->nodeList->item($this->counter);

        $log[] = $node->nodeValue;  

        //if the first nodeValue of the first b tag is stating who summarized the cases for the day subtract one from the number of 
        //links to be processed.
        
        if(null != $this->testFunc && $this->skipPass($node->nodeValue)){
            
            $this->next();
            return $this->current();
        }
        
        return $this->nodeList->item($this->counter);
    }

    public function key(){
        return $this->counter;
    }

    public function next(){
        $this->counter++;
    }

    public function rewind(){
        $this->counter = $this->startAt;
    }

    public function valid(){
        return null != $this->nodeList->item($this->counter);
    }

    public function skipPass($text){
        return call_user_func($this->testFunc,$text);
    }

    public function setTest($testFunc){
        $this->testFunc = $testFunc;
    }

    public function skip($number){
        $this->counter = $number;
        $this->startAt = $number;
    }
}