	<?php


class GenericDb {

	private $queryBuilder;

	public static function setUpQueryBuilder($conds/*, $table, $type = "select"*/) {
		if (!is_array($conds)) {
			$conds = [$conds];
		}

		$conditions = array();
		$sortConditions = array();
		$limitCondition = "";
		$columns = [];
		$values = [];
		//$updateFields = array();

		foreach($conds as $cond) {
			if (is_array($cond) || ($cond->type == "condition" && $cond->value != "ALL")) {
				$conditions[] = $cond;
			} else if ($cond->type == "sortCondition") {
				$sortConditions[] = $cond;
			} else if ($cond->type == "limitCondition") {
				$limitCondition = $cond;
			} else if ($cond->type == "insertCondition" && $type == "insert") {
				if (!in_array($cond->field, $columns)) {
						$columns[] = $cond->field;					
				}
				$values[$cond->rowId][$cond->field] = $cond->value;
			}// else if ($cond->type == "insertCondition" && $type == "update") {
			//	$updateFields[] = $cond;
			//}
		}

		$builder = new QueryBuilder();
		//$builder->setTable($table);
		//$builder->setType($type);
		$builder->setConditions($conditions);
		$builder->setSortConditions($sortConditions);
		$builder->setLimitCondition($limitCondition);
		$builder->setColumns($columns);
		$builder->setValues($values);
		//$builder->setUpdateFields($updateFields);

		return $builder;
	}

	public function query($type = "select") {
		$sql = $this->queryBuilder->compile();
		$results = MysqlDatabase::query($sql, $type);
		//if results has an error returned as json
		$results->getIterator();
	
		return $results;
	}

	public function getPageCount($resultsPerPage = null) {
		return $this->queryBuilder->getPageCount($resultsPerPage);
	}

	public function getCurrentPage() {
		return $this->queryBuilder->getCurrentPage();
	}

	public static function getSelectList($field, $model) {
			$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM {$model} ORDER BY {$field}");
			$parsedResults = array();
			foreach($dbResults as $result) {
					$parsedResults[] = $result[$field];
			}
			return $parsedResults;
	}

	function fromId($id, $model) {
		$query = MysqlDatabase::query("SELECT * FROM $model WHERE id = $id");
		$parsedQuery = array();
		foreach($query as $row) {
				$parsedQuery[] = $row;
		}
		return $parsedQuery[0];
	}
}