	<?php


// Assume that this model/helper might apply to several other objects.
class CaseReviewsDb {

	private $model;

	public function __construct($modelName = null) {
	
	}



	/**
	 * If the target objects has an id, then create it,
	 *  otherwise update it.
	 */
	function save() {
			if(false) {
				$this->insert($obj);
			} else {
				$this->update($obj);
			}
	}

	function insert() {
			$json = file_get_contents('php://input');
			$json = urldecode($json);
			$phpJson = json_decode($json);
			$columns = [];
			$values = [];

			foreach ($phpJson as $insertCondition) {
					if ($insertCondition->type == "insertCondition") {
							if (!in_array($insertCondition->field, $columns)) {
									$columns[] = $insertCondition->field;					
							}
							$values[$insertCondition->rowId][$insertCondition->field] = $insertCondition->value;
					}
			}

			//$values = array($phpJson->row);
			//$this->carCreate();
			$builder = new QueryBuilder();
			$builder->setTable("car");
			$builder->setType("insert");
			$builder->setColumns($columns);
			$builder->setValues($values);
			$sql = $builder->compile();
			
			
			return MysqlDatabase::query($sql, "insert");
	}



	function update($phpJson) {

		$conditions = array();
		$updateFields = array();

		foreach($phpJson as $cond) {
					/*if ($cond->type == "token" && $_SESSION['token'] != $cond->value) {
							return "Invalid session token";
					}*/

			if (is_array($cond) || $cond->type == "condition") {
				$conditions[] = $cond;
			} else if ($cond->type == "insertCondition") {
				$updateFields[] = $cond;
			}
			}
		
			$builder = new QueryBuilder();
			$builder->setTable("car");
			$builder->setType("update");
			$builder->setConditions($conditions);
			$builder->setUpdateFields($updateFields);
			$sql = $builder->compile();
			
			
			return MysqlDatabase::query($sql, "update");
	}


	public function delete($carId) {
		$json = file_get_contents('php://input');
		$json = urldecode($json);
		$condition = json_decode($json);

		$builder = new QueryBuilder();
		$builder->setTable("car");
		$builder->setType("delete");
		$builder->setConditions(array($condition));
		$sql = $builder->compile();
		MysqlDatabase::query($sql, "delete");
	}
	
	
	function fromId($carId) {
			$query = MysqlDatabase::query("SELECT * FROM car WHERE id = $carId");
			$parsedQuery = array();
			foreach($query as $row) {
					$parsedQuery[] = $row;
			}
			return $parsedQuery[0];
	}


	public function select($json = null) {

		$loadLimit = 10;

		// Perform a query for CARs in the database.
		// @todo - should return an iterable list of SObjects.
		// If conditons have been passed in then use them to build the query
		// otherwise use the next line for the query
		if(!empty($params)){
			$builder = new QueryBuilder();
			$builder->setTable("car");
			$builder->setConditions(json_decode($params));
			$sql = $builder->compile();

			return MysqlDatabase::query($sql);
		}
		else {
			return MysqlDatabase::query("SELECT * FROM car ORDER BY full_date DESC LIMIT " . $loadLimit);
		}

		$json = urldecode($json);
		$phpJson = json_decode($json);
		$conditions = array();
		$sortConditions = array();
		$limitCondition = "";

		//This removes queries that return everything
		foreach($phpJson as $cond) {
			if (is_array($cond) || ($cond->type == "condition" && $cond->value != "ALL")) {
				$conditions[] = $cond;
			} else if ($cond->type == "sortCondition") {
				$sortConditions[] = $cond;
			} else if ($cond->type == "limitCondition") {
				$limitCondition = $cond;
			}
		}

		$builder = new QueryBuilder();
		$builder->setTable("car");
		$builder->setType("select");
		$builder->setConditions($conditions);
		$builder->setSortConditions($sortConditions);
		$builder->setLimitCondition($limitCondition);
		$sql = $builder->compile();
		//print($sql);
		$results = MysqlDatabase::query($sql);
		//if results has an error returned as json
		$results->getIterator();
	
	
		return $results;
	}




	function getSelectList($field) {
			$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
			$parsedResults = array();
			foreach($dbResults as $result) {
					$parsedResults[] = $result[$field];
			}
			return $parsedResults;
	}



}