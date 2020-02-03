<?php
class CarDB{

    private $data;
    private $connection;

    function __construct($data){
        $this->data = $data;
    }

    function prepareData(){
        $this->subject1 = addslashes($this->data->getSubjects()[0]);
        $this->subject2 = addslashes($this->data->getSubjects()[1]);
        $this->summary = addslashes($this->data->getSummary());
        $this->result = addslashes($this->data->getCaseResult());
        $this->title = addslashes($this->data->getCaseTitle());
        $this->plaintiff = addslashes($this->data->getLitigants()[0]);
        $this->defendant = addslashes($this->data->getLitigants()[1]);
        $this->citation = addslashes($this->data->getCitation());
        $this->month = addslashes($this->data->getDecisionDate()[0]);
        $this->day = addslashes($this->data->getDecisionDate()[1]);
        $this->year = addslashes($this->data->getDecisionDate()[2]);
        $this->circut = addslashes($this->data->getCircutCourt());
        $this->majority = addslashes($this->data->getJudge());
        $this->judges = addslashes($this->data->getOtherJudges());
    }

    function connect(){
                // Create connection
        $this->connection = new mysqli(SERVER_NAME,USER_NAME,PASSWORD);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function insert(){
        echo "Connected successfully";
        $this->connection = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DATABASE_NAME);
        $query = "INSERT INTO cars (subject_1, subject_2, summary, result, title, plaintiff, defendant, citation, month, day, year, circut, majority, judges)
        VALUES ('$this->subject1','$this->subject2','$this->summary','$this->result','$this->title','$this->plaintiff','$this->defendant','$this->citation','$this->month', $this->day, $this->year,'$this->circut','$this->majority','$this->judges')";

        if ($this->connection->query($query) === TRUE) {
            echo "<br><strong>New record created successfully<br></strong>";
        } else {
            echo "<br><strong>ERROR CREATING RECORD: <br>" . $query . "<br>" . $this->connection->error . "<br></strong>";
        }
    }
    
    function close(){
        $this->connection->close();
    }
}