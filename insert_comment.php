<?php
 
include('config/connect.php');
include('dao/comment_dao.php');


$data = json_decode(file_get_contents('php://input'), true);

if (isset($data)) {

	$database = new Database();
	$conn = $database->getConnection();
	
	$commentDAO = new CommentDAO($conn);
	
	$comment = $data;

	$result = $commentDAO->insertComment($comment);

	echo json_encode($result);
}

?>