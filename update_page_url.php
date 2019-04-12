<?php

echo '<pre>';
ini_set('max_execution_time', 60);
date_default_timezone_set('America/Sao_Paulo');
echo "Start execution: " . date('Y-m-d H:i:s') . "\n";
$time_start = microtime(true);

include('service/filter.php');
include('config/connect.php');
include('dao/page_dao.php');

$database = new Database();
$conn = $database->getConnection();
$pageDAO = new PageDAO($conn);
$filter = new Filter();





$chapterId = $_GET['id_chapter'];
$pages = $pageDAO->fetchOldPages($chapterId);

if (count($pages) > 0) {
	$pagesHTML = $filter->filterPagesHTML($pages[0]['chapter_url'], $pages[0]["chapter_id"]);
	foreach ($pages as $page) {

		echo "chapter: " . $page["chapter_id"] . ", page: " . $page["id_page"] . "\n";

		foreach ($pagesHTML as $pageHTML) {

			if ($pageHTML["number"] == $page["number"]) {
				echo "pageHTML: " . $pageHTML["number"] . ", url: " . $pageHTML["page_url"] . "\n";
				$pageNewURL = $filter->filterPage($pageHTML["page_url"], $pageHTML["chapter_id"], $pageHTML["number"]);
				$page["page_url"] = $pageNewURL["page_url"];
				$pageDAO->updatePageURL($page);

				break;
			}
		}
	}
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "End execution: " . date('Y-m-d H:i:s') . "\n";
echo "Executed in $time seconds\n";

echo '</pre>';
/*
print_r($update);
//echo count($update);
echo '</pre>';*/

?>