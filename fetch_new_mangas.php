<?php
 
include('config/connect.php');
include('dao/manga_dao.php');
 
if (isset($_GET['manga_id'])) {
	$database = new Database();
	$conn = $database->getConnection();
	$mangaId = $_GET["manga_id"];

	$mangaDAO = new MangaDAO($conn);

	$final_res_manga = $mangaDAO->fetchNewMangas($mangaId);

	echo json_encode($final_res_manga);
}

?>