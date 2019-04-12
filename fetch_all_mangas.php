<?php
 
include('config/connect.php');
include('dao/manga_dao.php');
 

$database = new Database();
$conn = $database->getConnection();

$mangaDAO = new MangaDAO($conn);

$final_res_manga = $mangaDAO->fetchAllMangas();

echo json_encode($final_res_manga);

?>