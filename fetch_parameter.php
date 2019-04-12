<?php
 
include('config/connect.php');
include('dao/parameter_dao.php');

if (isset($_GET["parameter"])) {
 
	$database = new Database();
	$conn = $database->getConnection();

	$parameterDAO = new ParameterDAO($conn);

	$parameterName = $_GET["parameter"];

	$res = $parameterDAO->fetchParameter($parameterName);

	echo json_encode($res);
}

?>