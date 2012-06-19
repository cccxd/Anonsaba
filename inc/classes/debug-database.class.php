<?php
class DebugDatabase extends Database {
	public function GetAll($query) {
		echo 'GA: ',$query,"<br />\n";
		$stmt = $this->query($query);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function GetOne($query) {
		echo 'GO: ',$query,"<br />\n";
		$stmt = $this->query($query);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		unset($stmt);
		return (is_array($result)) ? array_shift($result) : $result;
	}
	public function GetRow($query) {
		echo 'GR: ',$query,"<br />\n";
		$stmt = $this->query($query.' LIMIT 1');
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		unset($stmt);
		return (!empty($results) && is_array($results)) ? array_shift($results) : false;
	}
	public function Execute($query) {
		echo 'EX: ',$query,"<br />\n";
		return $this->exec($query);
	}
}
?>
