<?php
 
include('config/connect.php');
include('dao/manga_dao.php');

if (isset($_GET["manga_id"]) && isset($_GET["device_uid"])) {
 
	$database = new Database();
	$conn = $database->getConnection();

	$mangaDAO = new MangaDAO($conn);

	$mangaId = $_GET["manga_id"];
	$deviceUid = $_GET["device_uid"];

	$manga = $mangaDAO->fetchMangaDetails($mangaId, $deviceUid);
	$mangaGenres = $mangaDAO->fetchMangaGenre($mangaId);

	$final_res_manga = array('manga' => $manga,
							 'manga_genres' => $mangaGenres
							);

	echo json_encode($final_res_manga);
}

?>