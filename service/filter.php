<?php

include('lib/simple_html_dom.php');

class Filter {


	function filterMangaDetails($url) {

		try {
			$html = file_get_html($url);
			if ((is_bool($html) === true)) {
				echo "mangaDetails fatal error caught: " . $url . "\n";
				return null;
			}
			$manga = array();
			$table = $html->find("#mangaproperties > table")[0];

			$genres = array();

			$manga['cover_url'] = trim($html->find("#mangaimg > img")[0]->src);
			$manga['name'] = trim($table->find("tbody > tr > td")[1]->plaintext);
			$manga['alternate_name'] = trim($table->find("tbody > tr > td")[3]->plaintext);
			$manga['year_release'] = trim($table->find("tbody > tr > td")[5]->plaintext);
			$manga['status'] = trim($table->find("tbody > tr > td")[7]->plaintext);
			$manga['author'] = trim($table->find("tbody > tr > td")[9]->plaintext);
			$manga['artist'] = trim($table->find("tbody > tr > td")[11]->plaintext);
			$manga['read_direction'] = trim($table->find("tbody > tr > td")[13]->plaintext);
			$manga['chapters'] = count($html->find("table#listing > tbody > tr")) - 1;
			
			foreach ($table->find('tbody > tr > td > a') as $genre) {

				$genres[] = $this->getGenreId(trim($genre->plaintext));
			}

			$manga['genres'] = $genres;
			$manga['description'] = trim($html->find('#readmangasum > p')[0]->plaintext);
			$manga['manga_url'] = $url;
			$manga['source_id'] = 0;
			$manga['last_update'] = 0;

			return $manga;
		} catch (Exception $e) {
			echo 'Caught mangaDetails exception: ',  $e->getMessage(), "\n";
			return null;
		}
	}

	function filterChapters($mangaURL, $chaptersLink) {

		try {
			$newChapters = array();
			$html = file_get_html($mangaURL);
			
			if ((is_bool($html) === true)) {
				echo "chapters fatal error caught: " . $mangaURL . "\n";
				return null;
			}

			$trs = $html->find("table#listing > tbody > tr");
					
			$table = $html->find("#mangaproperties > table")[0];
			$mangaName = trim($table->find("tbody > tr > td")[1]->plaintext);			

			foreach ($trs as $tr) {

				if ($tr->class != "table_head") {

					$chapterURL = "http://www.mangareader.net" . $tr->find("td > a")[0]->href;

					foreach ($chaptersLink as $chapterEntry) {

						if ($chapterEntry[1] == $chapterURL) {

							$chapter = array();
							$chapterName = $tr->find("td")[0]->plaintext;
							preg_match('"(?<= : ).*"', $chapterName, $matches);
							$chapter["name"] = $matches[0] . "";
							$chapter["chapter_url"] = $chapterURL;
							$chapter["number"] = $chapterEntry[0];

							if (trim($chapter["name"]) == "") {
								$chapter["name"] = "Chapter: " . $chapter["number"];
							}

							/*echo '<pre>';
							print_r($chapter);
							echo '</pre>';*/

							$newChapters[] = $chapter;

							break;
						}
					}
				}
			}
		} catch (Exception $e) {
			echo 'Caught chapters exception: ',  $e->getMessage(), "\n";	
			return null;
		}

		return $newChapters;
	}

	function filterPages($chapterURL, $chapterId) {

		$html = file_get_html($chapterURL);
		$pages = array();
		$options = null;
		
		try {
			$options = $html->find("#pageMenu > option");
		} catch (Exception $e) {
			echo 'Caught page exception: ',  $e->getMessage(), "\n";
			
			return $pages;
		}

		$pageCount = 0;
        foreach ($options as $option) {

            ++$pageCount;
            
            $pageHTML = "http://www.mangareader.net" . $option->value;
            
            $html2 = file_get_html($pageHTML);
            $imgURL = $html2->find("img")[0]->src;

            $page = array();
            $page["number"] = $pageCount;
            $page["page_url"] = $imgURL;
            $page["chapter_id"] = $chapterId;
            $pages[] = $page;
        }

        return $pages;
	}

	function filterPagesHTML($chapterURL, $chapterId) {

		try {
			$html = file_get_html($chapterURL);
			$pages = array();
			
			if ((is_bool($html) === true)) {
				echo "pageHTML fatal error caught: " . $chapterURL . "\n";
				return null;
			}

			$options = $html->find("#pageMenu > option");

			$pageCount = 0;
	        foreach ($options as $option) {

	            ++$pageCount;

	            $page["number"] = $pageCount;
	            $page["chapter_id"] = $chapterId;
	            $page["page_url"] = "http://www.mangareader.net" . $option->value;
	            $pages[] = $page;
	        }

	        return $pages;
        } catch (Exception $e) {
			echo 'Caught pagesHTML exception: ',  $e->getMessage(), "\n";
			return null;
		}
	}

	function filterPage($pageURL, $chapterId, $pageCount) {

		try {
	        $html = file_get_html($pageURL);

	        if ((is_bool($html) === true)) {
				echo "Caught page " . $pageCount . " fatal error: " . $pageURL . "\n";
				return null;
			}

	        $imgURL = $html->find("img")[0]->src;

	        $page = array();
	        $page["number"] = $pageCount;
	        $page["page_url"] = $imgURL;
	        $page["chapter_id"] = $chapterId;

	        return $page;
	    } catch (Exception $e) {
			echo 'Caught page ' . $pageCount . ' exception: ',  $e->getMessage(), "\n";
			return null;
		}
	}

	function getHTMLDOM($url) {

		return file_get_html($url);
	}

	function getGenreId($genre) {

		switch ($genre) {
		case "Action":
			return 1;
		case "Adventure":
			return 2;
		case "Comedy":
			return 3;
		case "Demons":
			return 4;
		case "Drama":
			return 5;
		case "Ecchi":
			return 6;
		case "Fantasy":
			return 7;
		case "Gender Bender":
			return 8;
		case "Harem":
			return 9;
		case "Historical":
			return 10;
		case "Horror":
			return 11;
		case "Josei":
			return 12;
		case "Magic":
			return 13;
		case "Martial Arts":
			return 14;
		case "Mature":
			return 15;
		case "Mecha":
			return 16;
		case "Military":
			return 17;
		case "Mystery":
			return 18;
		case "One shot":
			return 19;
		case "Psychological":
			return 20;
		case "Romance":
			return 21;
		case "School Life":
			return 22;
		case "Sci-Fi":
			return 23;
		case "Seinen":
			return 24;
		case "Shoujo":
			return 25;
		case "Shoujoai":
			return 26;
		case "Shounen":
			return 27;
		case "Shounenai":
			return 28;
		case "Slice of Life":
			return 29;
		case "Smut":
			return 30;
		case "Sports":
			return 31;
		case "Super Power":
			return 32;
		case "Supernatural":
			return 33;
		case "Tragedy":
			return 34;
		case "Vampire":
			return 35;
		case "Yaoi":
			return 36;
		case "Yuri":
			return 37;
		}

		return -1;
	}
}



?>