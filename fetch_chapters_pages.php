<?php
 
include('config/connect.php');
include('dao/page_dao.php');


$data = json_decode(file_get_contents('php://input'), true);

if (isset($data)) {

	$database = new Database();
	$conn = $database->getConnection();
	
	$pageDAO = new PageDAO($conn);

	$chapters = $data["chaptersId"];

	$return = $pageDAO->fetchChaptersPages($chapters);

	echo json_encode($return);
}

?>