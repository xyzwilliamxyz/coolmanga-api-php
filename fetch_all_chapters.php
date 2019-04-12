<?php
 
include('config/connect.php');
include('dao/chapter_dao.php');

if (isset($_GET['manga_id'])) {

	$database = new Database();
	$conn = $database->getConnection();
	$mangaId = $_GET["manga_id"];

	$chapterDAO = new ChapterDAO($conn);

	$final_res_chapter = $chapterDAO->fetchMangaChapters($mangaId);

	echo json_encode($final_res_chapter);
}

?>