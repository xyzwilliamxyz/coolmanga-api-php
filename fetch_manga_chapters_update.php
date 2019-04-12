<?php
 
include('config/connect.php');
include('dao/chapter_dao.php');


$data = json_decode(file_get_contents('php://input'), true);

if (isset($data)) {

	$database = new Database();
	$conn = $database->getConnection();
	
	$chapterDAO = new ChapterDAO($conn);

	$jsonReturn = array();

	foreach ($data as $manga) {

		$mangaId = $manga["mangaId"];
		$chapters = $manga["chapters"];

		$return = $chapterDAO->fetchMangaChaptersUpdate($mangaId, $chapters);

		if (count($return) > 0) {

			$jsonReturn[] = $return;
		}
	}

	echo json_encode($jsonReturn);
}

?>