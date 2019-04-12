<?php
 
include('config/connect.php');
include('dao/comment_dao.php');

if (isset($_GET['manga_id']) && isset($_GET['number'])) {

	$database = new Database();
	$conn = $database->getConnection();
	$mangaId = $_GET["manga_id"];
	$number = $_GET["number"];

	$commentDAO = new CommentDAO($conn);

	$final_res_comments = $commentDAO->fetchRecentComments($mangaId, $number);
	

	echo json_encode($final_res_comments);
}

?>