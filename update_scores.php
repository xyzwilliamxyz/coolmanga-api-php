<?php

date_default_timezone_set('America/Sao_Paulo');
echo "Start execution: " . date('Y-m-d H:i:s') . "\n";
$time_start = microtime(true);

//include('config/connect.php');
//include('dao/manga_dao.php');


//$database = new Database();
//$conn = $database->getConnection();
//$mangaDAO = new MangaDAO($conn);


$affectedRows = $mangaDAO->updateScores();

echo "Updated manga: " . $affectedRows . '\n';

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "End execution: " . date('Y-m-d H:i:s') . "\n";
echo "Executed in $time seconds\n";

?>