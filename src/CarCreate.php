<?php

function carCreatePage($carId = -1) {
    $carJson;
    $update = false;

    if ($carId != -1) {
        $car = getCarById($carId);
        $carJson = json_encode($car);
        $update = true;
    }

    $template = attachTemplateFiles();

    $existingOptionFields = ["subject_1", "plaintiff", "circut", "majority"];
    $newFields = ["title", "subject_2", "summary", "result", "defendant", "citation", "judges", "url"];
    $listOptions = [];

    foreach ($existingOptionFields as $field) {
        $listOptions[$field] = getSelectList($field);
    }

    $newFieldsJson = json_encode($newFields);
    $listOptionsJson = json_encode($listOptions);

    $content = Template::renderTemplate("car-create", array(
        'update' => $update,
        'car' => $carJson,
        'newFieldsJson' => $newFieldsJson,
        'listOptionsJson' => $listOptionsJson
    ));

    return $template->render(array(
        "defaultStageClass" 	=> "not-home", 
        "content" 				=> $content,
        "doInit"				=> false
    ));
}

function submitNewCar() {
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
    $builder->setColumns($columns);
    $builder->setValues($values);
    $sql = $builder->compile("insert");
    MysqlDatabase::query($sql, "insert");
}

function updateCar() {
    $json = file_get_contents('php://input');
    $json = urldecode($json);
	$phpJson = json_decode($json);
	$conditions = array();
	$updateFields = array();

	//This removes queries that return everything
	foreach($phpJson as $cond) {
		if (is_array($cond) || $cond->type == "condition") {
			$conditions[] = $cond;
		} else if ($cond->type == "insertCondition") {
			$updateFields[] = $cond;
		}
    }
    
    $builder = new QueryBuilder();
    $builder->setTable("car");
    $builder->setConditions($conditions);
    $builder->setUpdateFields($updateFields);
    $sql = $builder->compile("update");
    MysqlDatabase::query($sql, "update");
}

function getCarById($carId) {
    $query = MysqlDatabase::query("SELECT * FROM car WHERE id = $carId");
    $parsedQuery = array();
    foreach($query as $row) {
        $parsedQuery[] = $row;
    }
    return $parsedQuery[0];
}

function attachTemplateFiles() {
    $carDir = dirname(__DIR__, 1);
    Template::addPath($carDir . "/templates");

    $template = Template::loadTemplate("webconsole");

    $css = array(
        "active" => true,
        "href" => "/modules/car/css/carCreateStyles.css"
    );
    
    $template->addStyle($css);

    $js = array(
        array(
            "src" => "/modules/car/src/FormSubmission.js"
        ),
        array(
            "src" => "/modules/car/src/FormParser.js"
        ),
        array(
            "src" => "/modules/car/src/DBQuery.js"
        ),
        array(
            "src" => "/modules/car/src/BaseComponent.js"
        ),
        array(
            "src" => "/modules/car/src/CreateCarUI.js"
        ),
        array(
            "src" => "/modules/car/src/CarCreateModule.js"
        )
    );

    $template->addScripts($js);

    return $template;
}

function getSelectList($field) {
    $dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM car ORDER BY {$field}");
    $parsedResults = array();
    foreach($dbResults as $result) {
        $parsedResults[] = $result[$field];
    }
    return $parsedResults;
}

?>