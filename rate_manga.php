<?php
 
include('config/connect.php');
include('dao/manga_dao.php');

if (isset($_GET['manga_id']) && isset($_GET['rate']) && isset($_GET['device_uid'])) {

	$database = new Database();
	$conn = $database->getConnection();
	$mangaId = $_GET["manga_id"];
	$rate = $_GET["rate"];
	$deviceUid = $_GET["device_uid"];

	$mangaDAO = new MangaDAO($conn);

	$response = $mangaDAO->insertOrUpdateMangaRate($mangaId, $deviceUid, $rate);

	echo json_encode($response);
}

?>