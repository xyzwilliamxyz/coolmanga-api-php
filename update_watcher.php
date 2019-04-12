<?php

//echo '<pre>';
//ini_set('max_execution_time', 3600);
date_default_timezone_set('America/Sao_Paulo');
echo "Start execution: " . date('Y-m-d H:i:s') . "\n";
$time_start = microtime(true);

include('service/filter.php');
include('config/connect.php');
include('dao/manga_dao.php');
include('dao/chapter_dao.php');
include('dao/page_dao.php');



$database = new Database();
$conn = $database->getConnection();
$mangaDAO = new MangaDAO($conn);
$chapterDAO = new ChapterDAO($conn);
$pageDAO = new PageDAO($conn);
$filter = new Filter();

$update = array();
$urls = array();
$html = $filter->getHTMLDOM('http://www.mangareader.net/latest');


foreach($html->find('tr.c2') as $element) {

    $mangaName = trim($element->find('a.chapter')[0]->plaintext);
    $urls[$mangaName] = "http://www.mangareader.net" . trim($element->find('a.chapter')[0]->href);

    foreach ($element->find('a.chaptersrec') as $subelement) {

    	$update[$mangaName][] = array(trim(str_replace($mangaName, "", $subelement->plaintext)) , "http://www.mangareader.net" . $subelement->href);
    }
}

foreach (array_keys($update) as $manga) {

	$mangaDetails = $mangaDAO->fetchMangaDetailsByName($manga);
 
	if ($mangaDetails == null) {

		$mangaFiltered = $filter->filterMangaDetails($urls[$manga]);

		if ($mangaFiltered == null) {

			echo "manga Details not found: " . $urls[$manga] . "\n";
			continue;
		}

		$mangaDAO->insertManga($mangaFiltered);
		$mangaId = $conn->lastInsertId();

		foreach ($mangaFiltered['genres'] as $genre) {
			$mangaDAO->insertMangaGenre($mangaId, $genre);
		}

		$mangaDetails['id_manga'] = $mangaId;
		echo "manga_id inserted: " . $mangaId . "\n";
	}

	echo "manga: " . $manga . "(" . $mangaDetails['id_manga'] . ")\n";


	$newChapters = null;
	foreach ($update[$manga] as $chapter) {

		echo "chapter: " . $chapter[0];
		$chapterDetails = $chapterDAO->fetchMangaChapterByMangaIDAndChapterNumber($mangaDetails["id_manga"], $chapter[0]);

		if ($chapterDetails == null) {

			if ($newChapters == null) {
				$newChapters = $filter->filterChapters($urls[$manga], $update[$manga]);
			}

			if ($newChapters == null) {

				echo "chapters page doesn't exist.\n";
				break;
			}

			// get the associated chapter
			foreach ($newChapters as $nc) {

				if ($chapter[0] == $nc["number"]) {
					$chapterDetails = $nc;
					$chapterDetails["manga_id"] = $mangaDetails["id_manga"];
					break;
				}
			}

			if ($chapterDetails == null) {

				echo "chapter doesn't exist!\n";
				continue;
			}

			$chapterDAO->insertChapter($chapterDetails);
			$chapterDetails["id_chapter"] = $conn->lastInsertId();
			$chapterDetails["done"] = 0;

			echo "\nid_chapter inserted: " . $chapterDetails["id_chapter"] . "\n";
		} else {
			 echo "(" . $chapterDetails['id_chapter'] . ")\n";
		}

		/*if ($chapterDetails["done"] == 0) {
			$pagesHTML = $filter->filterPagesHTML($chapterDetails['chapter_url'], $chapterDetails["id_chapter"]);


			if ($pagesHTML == null) {
				echo "pages chapter dosn't exist!\n";
				continue;
			}

			$pagesSaved = $pageDAO->fetchChapterPages($chapterDetails["id_chapter"]);

			foreach ($pagesHTML as $pageHTML) {

				echo "pageHTML: " . $pageHTML["number"] . ", url: " . $pageHTML["page_url"] . "\n";

				$saved = false;
				foreach ($pagesSaved as $pageSaved) {

					if ($pageHTML["number"] == $pageSaved["number"]) {
						$saved = true;
						echo "pageSaved: " . $pageSaved["number"] . "\n";
						break;
					}
				}

				if (!$saved) {
					$page = $filter->filterPage($pageHTML["page_url"], $pageHTML["chapter_id"], $pageHTML["number"]);

					if ($page == null) {
						echo "page not found: " . $pageHTML["page_url"] . "\n";
						continue;
					}

					$pageDAO->insertPage($page);
					echo "page_id inserted: " . $conn->lastInsertId() . "\n";
				}
			}

			$chapterDAO->updateChapterDone($chapterDetails["id_chapter"]);
		}*/
	}
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "End execution: " . date('Y-m-d H:i:s') . "\n";
echo "Executed in $time seconds\n";

echo '\n\n\n\n';
include('update_scores.php');



//echo '</pre>';
/*
print_r($update);
//echo count($update);
echo '</pre>';*/

?>