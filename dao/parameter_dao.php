<?php
    
class ParameterDAO {
	
    public $conn;
    
    function __construct($conn) {
    	
    	$this->conn = $conn;
    }

    function fetchParameter($parameterName) {

        $query = "SELECT p.name, p.value FROM parameter p WHERE p.name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $parameterName, PDO::PARAM_STR);
        $stmt->execute();

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res;
    }

    function fetchAllParameters() {

        $query = "SELECT p.name, p.value FROM parameter p";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $parameters = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parameters[] = $rec;
        }

        return $parameters;
    }
}


?>