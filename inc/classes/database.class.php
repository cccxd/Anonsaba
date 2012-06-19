<?php
class Database extends PDO {
	public function GetAll($query) {
		$stmt = $this->query($query);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function GetOne($query) {
		$stmt = $this->query($query);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		unset($stmt);
		return (is_array($result)) ? array_shift($result) : $result;
	}
	public function GetRow($query) {
		$stmt = $this->query($query.' LIMIT 1');
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		unset($stmt);
		return (!empty($results) && is_array($results)) ? array_shift($results) : false;
	}
	public function Execute($query) {
		return $this->exec($query);
	}
	public function qstr($var) {
		$quote = $this->quote($var);
		return $quote;
	}
	public function ErrorMsg() {
		$errormessage = array($this->errorInfo());
		return $errormessage[0][2];
	}
}
?>
