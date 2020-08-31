	<?php


class GenericDb {




	public static function getSelectList($field, $model) {
			$dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM {$model} ORDER BY {$field}");
			$parsedResults = array();
			foreach($dbResults as $result) {
					$parsedResults[] = $result[$field];
			}
			return $parsedResults;
	}


}