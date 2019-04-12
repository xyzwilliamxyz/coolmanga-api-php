<?php
 
include('connect.php');
 
$database = new Database();
$conn = $database->getConnection();


// manga
$query = "SELECT m.id_manga, m.name, m.alternate_name, m.chapters, m.author, m.artist, m.status, m.rank, m.read_direction,
                m.year_release, m.description, m.cover_url, m.source_id, m.last_update, m.last_view, m.manga_url, m.is_favorite
                FROM manga m LIMIT 10";
$stmt = $conn->prepare( $query );
$stmt->execute();

$jsonObj= array();
while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$jsonObj[] = $rec;
}
$final_res_manga = $jsonObj;

// genre
$query = "SELECT id_genre, description FROM genre";
$stmt = $conn->prepare( $query );
$stmt->execute();

$jsonObj= array();
while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$jsonObj[] = $rec;
}
$final_res_genre = $jsonObj;

// manga_genre
$query = "SELECT manga_id, genre_id FROM manga_genre LIMIT 100";
$stmt = $conn->prepare( $query );
$stmt->execute();

$jsonObj= array();
while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$jsonObj[] = $rec;
}
$final_res_manga_genre = $jsonObj;



$jsonResult = array(
	"mangas" => $final_res_manga,
	"genre" => $final_res_genre,
	"manga_genre" => $final_res_manga_genre
	);
echo json_encode($jsonResult);
  
?>