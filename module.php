<?php

use \Html\HtmlLink;


class CarModule extends Module {


	public function __construct(){
		parent::__construct();
		$this->routes = carRoutes();
		$this->files = array("Car.php","CarUrlParser.php","CarParserException.php","CarDB.php");
		$this->name = "car";
	}

}


function carRoutes() {
	return array(
		"load-cars" => array(
			"callback" => "loadCarsData",
			"Content-Type" => "application/json"
		),
		"view-page" => array(
			"callback" => "viewPage",
			"Content-Type" => "text/html"
		),
		"insert-car-data" => array(
			"callback" => "insertCarData",
			"Content-Type" => "application/json"
		),
		"test-car-urls" => array(
			"callback" => "getCandidateUrlOutput",
			"Content-Type" => "application/json"
		),
		"car-urls" => array(
			"callback" => "getCarUrlsByDate",
			"Content-Type" => "application/json"
		),
		"car-urls-range" => array(
			"callback" => "getUrlsRange",
			"Content-Type" => "application/json"
		),
		"query-db" => array(
			"callback" => "queryDb",
			"Content-Type" => "application/json"
		)
	);
}

function loadPage($month,$day,$year) {

	//$url = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";

	//Crate a new date formated to be passed to the CarUrlParser and pass it to the request object
	//Create new date from numeric values only
	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));
	$urlParser = new CarUrlParser($urlDate);
	$resp = $urlParser->makeRequests();

	//Pass the body of the page to the DocumentParser
	if($resp != null){
	$page = new DocumentParser($resp->getBody());
		//We are only concerned with the content located in the 'mw-content-text' class of the page
		$fragment = $page->fromTarget("mw-content-text");

		return $fragment;
	}
}

function viewPage($month,$day,$year){
	$page = loadPage($month,$day,$year);
	if($page == null){
		throw new CarParserException("The page at that url is null");
	}
	return $page->saveHTML();
}

function loadCarsData($xml){

	$subjects = $xml->getElementsByTagName("b");	
	
	$links = $xml->getElementsByTagName("a");

	$errors = array();
	$nullSubjects = array();
	
	$aNumbers = array();
	$cars = array();

	$MAX_PROCESS_LINKS = count($subjects)-1;
	
	for($i = 0; $i < $MAX_PROCESS_LINKS; $i++) {

		//We want to skip the first p tag which is the name of the person summarizing the cases
		$subject = $subjects->item($i+1);

		//We are skipping the first to links on the page because they are links to the author and the comments
		$link = $links->item($i+2);

		//if($subject != null && $link != null) //for testing purposes
		$car = new Car($subject,$link);	
		try{
			//if($car != null) //for testing purposes
			$car->parse();
		}catch(CarParserException $e){
			//do something with the $e->stuff
			$errors[] = $e;
			$nullSubjects[] = $subject;
		}
		$cars[] = $car;


	}
	// print("ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---ERRORS---");
	// var_dump($errors);
	// print("NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---NULL SUBJECTS---");
	// var_dump($nullSubjects);
	
	return $cars;
}

//route that takes an int number of days starting today tho attempts to load urls for without execution of calluserfunc line

function getCandidateUrlOutput($days){
	set_time_limit(900);

	$output = array();

	//output = getoutput()

	//return output
	$urlDate = new DateTime();
	for($i = 0; $i < $days; $i++){
		$urlDate->modify("-1 day");
		$urlParser = new CarUrlParser($urlDate);
		$urlParser->makeRequests();	
		$output[] = $urlParser->getOutput();
	}
	return $output;
}

//dump in sql format
function insertCarData($days){
	set_time_limit(0);
	$startTime = time();

	$urlDate = new DateTime();
	for($i = 0; $i < $days; $i++){
		$urlDate->modify("-1 day");
		//throw an exception if 2018
		$urlDateFormat = $urlDate->format("n j Y");
		$xml = call_user_func_array("loadPage",explode(" ",$urlDateFormat));

		if($xml == null){
			$status = "not found";
		}else{
			$cars = loadCarsData($xml);
			for($j = 0; $j < count($cars); $j++){
				$cn = $j+1;
				$runTime = time_elapsed(time() - $startTime);
				$date = $urlDate->format("F j, Y");
				print("<br><strong>-----CASE #".$cn." for ".$date."-----ELAPSED TIME ".($runTime). "</strong><br>");
				insert($cars[$j]);
				// displayCarOutput($cars[$j]);
			}
			$status = $cars[$j]->url."everything went ok";
		}
		echo  nl2br ("<br><strong>THE CARS DATE: ".$urlDateFormat."---STATUS: ".$status." ELAPSED TIME ".($runTime)."</strong><br>");
	}
}

function getCarUrlsByDate($month = null,$day = null,$year = null) {
	set_time_limit(5);

	$urls = array("someUrl","someotherurl");

	$urlDate = DateTime::createFromFormat ( "n j Y" , implode(" ",array($month,$day,$year)));
	$urlParser = new CarUrlParser($urlDate);


	$urls = $urlParser->getUrls();


	return $urls;
}
function getUrlsRange($days){
	//gets the candidate urls for a specified number of days

	$date = new DateTime();
	$urls = array();

	for($i = 0; $i <= $days; $i++){
		$date->modify("-1 day");
		$urlParser = new CarUrlParser($date);


		//Standard class to hold metadata for the urls  
		$data =  new StdClass();
		$data->date = $date->format("F j, Y");
		$data->iterationNumber = $i;
		$data->urls = $urlParser->getUrls();

		$urls[] = $data;

	}
	
	return $urls;
}


function displayCarOutput($car){
	print("<strong>SUBJECT #1:</strong> ".$car->subject_1."<BR>");
	print("<strong>SUBJECT #2:</strong> ".$car->subject_2."<BR>");
	print("<strong>SUMMARY:</strong> ". $car->summary."<br>");
	print("<strong>CASE RESULT:</strong> ". $car->result."<br>");
	print("<strong>CASE TITLE:</strong>". $car->title."<br>");
	print("<strong>PLAINTIFF:</strong> ". $car->plaintiff."<br>");
	print("<strong>DEFENDANT:</strong> ". $car->defendant."<br>");
	print("<strong>CITATION:</strong> ". $car->citation."<br>");
	print("<strong>DECISION DATE:</strong> ". $car->month." ".$car->day.", ".$car->year."<br>");
	print("<strong>CIRCUT COURT:</strong> ". $car->circut."<br>");
	print("<strong>JUDGE:</strong> ". $car->majority."<br>");
	print("<strong>OTHER JUDGES:</strong> ". $car->judges."<br>");
	print("<strong>URL TO THE PAGE:</strong> ". $car->url."<br>");
}

function time_elapsed($secs){
    $bit = array(
        ' Years' => $secs / 31556926 % 12,
        ' Weeks' => $secs / 604800 % 52,
        ' Days' => $secs / 86400 % 7,
        ' Hours' => $secs / 3600 % 24,
        ' Minutes' => $secs / 60 % 60,
        ' Seconds' => $secs % 60
        );
       
    foreach($bit as $k => $v)
        if($v > 0)$ret[] = $v . $k;
       
    return join(' ', $ret);
}
function queryDb($json){

	//from the console

	// var search = {field:"summary",op:"LIKE",value:"duii"})
	// VM8917:1 Uncaught SyntaxError: Unexpected token ')'
	// var search = {field:"summary",op:"LIKE",value:"duii"}
	// undefined
	// var params = JSON.stringify(search);
	// undefined
	// params
	// "{"field":"summary","op":"LIKE","value":"duii"}"
	// var mySearch = fetch("/query-db",{method:"post",body:params});
	// undefined
	// mySearch.then(function(result){console.log(result))}
	// VM9853:1 Uncaught SyntaxError: Unexpected token ')'




//function queryDb($field,$operator,$search){
	$requestBody = '[{"field":"summary","op":"LIKE","value":"duii"}]';
	$json = json_decode($requestBody);

	

	$rows = array();

	$queryObj = selectClause($field).whereClause($json);
	//print($queryObj);exit;

	$connection = new mysqli(HOST_NAME,USER_NAME,USER_PASSWORD, DATABASE_NAME);

	if ($connection->connect_error) {
		die("Connection failed: " . $connection->connect_error);
	}
	$result = $connection->query($queryObj);

	if ($result != null) {
		print_r("NUMBER OF ROWS ".$result->num_rows."<br>");
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$rows[] = $row;
			}
			print("NUMBER OF ROWS ".count($rows));
		}
		return $rows;
	} else {
		echo "<br><strong>ERROR RETRIEVING RECORD: <br>" . $queryObj . "<br>" . $connection->error . "<br></strong>";
	}
}

function selectClause($field){
	$tableName = "car";
	$selectFields = array();

	// $fields = array(
	// "subject_1","subject_2",
	// "summary","result",
	// "title","plaintiff",
	// "defendant","citation",
	// "month","day","year",
	// "circut","majority","judges");

	// foreach($fields as $f){
	// 	if($f == $field){
	// 		$selectFields[] = $f;
	// 	}
	// }

	// if(count($selectFields) == 0){
	// 	throw new Exception("No valid fields provided");
	// }
	// if(count($selectFields) == 1){
	// 	$fieldsList = $selectFields[0];
	// }
	// if(count($selectFields) >= 2){
	// 	$fieldsList = implode(",",$selectFields);
	// }

	//return "SELECT $fieldsList FROM $tableName";
	return "SELECT * FROM $tableName";
}

// s


function whereClause($conditions){



   $where = "";  // Prepare to build a SQL WHERE clause
   $tmp = array();
   
	foreach($conditions as $c){
		$field = $c->field;
		$op = $c->op;
		$value = $c->value;

		if(is_int($value)){
			$tmp []= sprintf("%s %s %d",$field,$op,$value);
		} else if($op == 'LIKE'){
			$tmp [] = sprintf("%s %s '%%%s%%'",$field,$op,$value);
		} else {
			$tmp [] = sprintf("%s %s '%s'",$field,$op,$value);
		}
	}

	$where .= " WHERE ".implode(' AND ',$tmp);

	return $where;
}
