	<?php


// Assume that this model/helper might apply to several other objects.
class CaseReviewsDb extends GenericDb {

	private $model;
	private $table = "car";

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

	function insert($json) {
		$builder = QueryBuilder::fromJson($json);
		$builder->setTable($this->table);
		$builder->setType("insert");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql, "insert");

		return $results;
	}



	function update($json) {
		$builder = QueryBuilder::fromJson($json);
		$builder->setTable($this->table);
		$builder->setType("update");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql, "update");

		return $results;
	}


	public function delete($json) {
		/*$conds = $this->parseJson($json);
		$test = parent::setUpQueryBuilder([$conds], $this->table, "delete");
		return parent::query("delete");*/
		$builder = QueryBuilder::fromJson($json);
		$builder->setTable($this->table);
		$builder->setType("delete");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql, "delete");

		return $results;

		/*$condition = $this->parseJson($json);

		$builder = new QueryBuilder();
		$builder->setTable("car");
		$builder->setType("delete");
		$builder->setConditions(array($condition));
		$sql = $builder->compile();
		MysqlDatabase::query($sql, "delete");*/
	}


	public function select($json = null) {
		$table = $this->table;

		if ($json === null) {
			return MysqlDatabase::query("SELECT * FROM $table ORDER BY full_date DESC LIMIT 10");
		}

		/*$conds = $this->parseJson($json);

		foreach($conds as $cond) {
			if ($cond->type == "limitCondition") {
				$cond->type = "none";
			}
		}*/

		//parent::setUpQueryBuilder($conds, $table);
		$builder = QueryBuilder::fromJson($json);
		$builder->setTable($table);
		$builder->setType("select");
		$sql = $builder->compile();
		$results = MysqlDatabase::query($sql);

		return $results;
	}

	function getNumOfPages($json) {
		$conds = $this->parseJson($json);
		parent::setUpQueryBuilder($conds, $this->table);
		$count = parent::getPageCount();

		return $count;
	}

	function getNextPage($json) {
		$conds = $this->parseJson($json);
		parent::setUpQueryBuilder($conds, $this->table);
		$page = parent::getCurrentPage();

		return $page;
	}


	function parseJson($json) {
		$json = urldecode($json);
		return json_decode($json);
	}
}