<?php
require_once "classConnection.php";

class DB {
	private $connection;
	
	public function __construct() {
		$this->connection = new Connection();
	}
	
	function getDBName(){
		$result = $this->connection->query("SELECT DATABASE()");
		$row = $result->fetch_row();
		return $row[0];
	}
	
	function getTables(){
		$dbName = $this->getDBName();
		$result_set = $this->connection->query("SHOW TABLES IN ".$dbName);
		$res_arr = array();
		$i = 0;
		while ($row = $result_set->fetch_assoc()) {
			$res_arr[$i] = $row["Tables_in_".$dbName];
			$i++;
		}
		return $res_arr;
	}
	
	function getFields($tblName){
		$dbName = $this->getDBName();
		$result_set = $this->connection->query("SHOW COLUMNS IN `".$tblName."` IN ".$dbName);
		$res_arr = array();
		$i = 0;
		while ($row = $result_set->fetch_assoc()) {
			$res_arr[$i] = $row["Field"];
			$i++;
		}
		return $res_arr;
	}
	
	function checkRow($tblName, $field, $value){
		$result_set = $this->connection->query("SELECT COUNT(*) FROM `".$tblName."` WHERE `".$field."` = '".$value."'");
		while ($row = $result_set->fetch_assoc()) {
			if ($row["COUNT(*)"] > 0) return true;
				else return false;
		}
	}
	
	function truncateTable($tblName){
		$result = $this->connection->query("TRUNCATE TABLE `".$tblName."`");
	}
	
	function getRows($tblName, $field, $value){
		$result_set = $this->connection->query("SELECT * FROM `".$tblName."` WHERE `".$field."` = '".$value."'");
		
		$res_arr = array();
		$i = 0;
		
		$fields = $this->getFields($tblName);
		
		while ($row = $result_set->fetch_assoc()) {
			$temp_arr = array();
			
			for ($j = 0; $j < count($fields); $j++){
				$temp_arr[$fields[$j]] = $row[$fields[$j]];
			}
			
			$res_arr[$i] = $temp_arr;
			$i++;
		}
		return $res_arr;
	}
	
	function countRows($tblName, $fieldsAndValues){
		$q = "SELECT COUNT(*) FROM `".$tblName."` WHERE ";
		
		$counter = 0;
		foreach ($fieldsAndValues as $field => $value) {
			$q .= "`".$field."` = '".$value."'";
			$counter++;
			if ($counter != count($fieldsAndValues)) $q .= " AND ";
		}
		
		$result_set = $this->connection->query($q);
		
		while ($row = $result_set->fetch_assoc()) {
			return $row["COUNT(*)"];
		}
	}
	
	function countAllRows($tblName) {
		$result_set = $this->connection->query("SELECT COUNT(*) FROM `".$tblName."` WHERE 1");
		
		while ($row = $result_set->fetch_assoc()) {
			return $row["COUNT(*)"];
		}
	}
	
	function getListRows($tblName, $sort = true, $orderBy = "id", $fieldsAndValues = false, $limit = false, $offset = 0, $number = 0){
		if ($sort == true) $sort = "ASC";
		else $sort = "DESC";
		$q = $q = "SELECT * FROM `".$tblName."`";
		if ($fieldsAndValues != false) {
			$q .= " WHERE ";
			$counter = 0;
			foreach ($fieldsAndValues as $field => $value) {
				$q .= "`".$field."` = '".$value."'";
				$counter++;
				if ($counter != count($fieldsAndValues)) $q .= " AND ";
			}
		}
		$q .= " ORDER BY `".$orderBy."` ".$sort;
		if ($limit == true) $q .= " LIMIT ".$offset.",".$number;
		
	//	echo $q;
		$result_set = $this->connection->query($q);
		
		$res_arr = array();
		$i = 0;
		
		$fields = $this->getFields($tblName);
		
		while ($row = $result_set->fetch_assoc()) {
			$temp_arr = array();
			
			for ($j = 0; $j < count($fields); $j++){
				$temp_arr[$fields[$j]] = $row[$fields[$j]];
			}
			
			$res_arr[$i] = $temp_arr;
			$i++;
		}
		
		return $res_arr;
	}
	
	function addRow($tblName, $values) {
		$fields = $this->getFields($tblName);
		$q = "INSERT INTO `".$tblName."` (";
		
		for ($i = 0; $i < count($fields); $i++){
			if ($i != (count($fields)-1)){
				$q .= "`".$fields[$i]."`, ";
			}
			else {
				$q .= "`".$fields[$i]."`)";
			}
		}
		
		$q .= " values(";
		
		for ($i = 0; $i < count($values); $i++){
			if ($i != (count($values)-1)){
				$q .= "'".$values[$i]."', ";
			}
			else {
				$q .= "'".$values[$i]."')";
			}
		}
		
		$this->connection->query($q);
	}
	
	function updateRow($tblName, $needed_field, $needed_value, $fieldsAndValues){
		$q = "UPDATE `".$tblName."` SET ";
		
		$counter = 0;
		foreach ($fieldsAndValues as $field => $value) {
			$q .= "`".$field."` = '".$value."'";
			$counter++;
			if ($counter != count($fieldsAndValues)) $q .= ", ";
		}
		$q .= " WHERE `".$needed_field."` = '".$needed_value."'";
		
		$this->connection->query($q);
	}
	
	function deleteRow($tblName, $field, $value){
		$this->connection->query("DELETE FROM `".$tblName."` WHERE `".$field."` = '".$value."'");
	}
	
	function getFieldSum($tblName, $field) {
		$q = "SELECT sum(`".$field."`) FROM `".$tblName."`";
		// echo $q;
		$result_set = $this->connection->query($q);
		while ($row = $result_set->fetch_assoc()) {
			return $row["sum(`".$field."`)"];
		}
	}
}
?>