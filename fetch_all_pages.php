<?php
 
include('config/connect.php');
include('dao/page_dao.php');

if (isset($_GET['chapter_id'])) {

	$database = new Database();
	$conn = $database->getConnection();
	$chapterId = $_GET["chapter_id"];

	$pageDAO = new PageDAO($conn);

	$final_res_pages = $pageDAO->fetchChapterPages($chapterId);

	echo json_encode($final_res_pages);
}

?>