<?php
 
include('config/connect.php');
include('dao/genre_dao.php');

$database = new Database();
$conn = $database->getConnection();

$genreDAO = new GenreDAO($conn);

$final_res_genre = $genreDAO->fetchAllGenres();
echo json_encode($final_res_genre);

?>