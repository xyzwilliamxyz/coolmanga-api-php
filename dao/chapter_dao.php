<?php
    
class ChapterDAO {
	
    public $conn;
    
    function __construct($conn) {
    	
    	$this->conn = $conn;
    }

    function fetchMangaChapterByMangaIDAndChapterNumber($mangaId, $chapterNumber) {
        
        $query = "SELECT id_chapter, number, name, chapter_url, release_date, manga_id FROM chapter WHERE manga_id = ? AND number = ?";
        $stmt = $this->conn->prepare( $query  );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->bindParam(2, $chapterNumber, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }
    
    function fetchMangaChaptersCount($mangaId) {
    	
        $query = "SELECT COUNT(id_chapter) FROM chapter WHERE manga_id = ?";
        $stmt = $this->conn->prepare( $query  );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        $count = $stmt->fetch(PDO::FETCH_ASSOC);

        return $count;
    }
    
    function fetchMangaChapters($mangaId) {
    	
        $query = "SELECT id_chapter, number, name, chapter_url, release_date, manga_id FROM chapter WHERE manga_id = ? ORDER BY number DESC";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->execute(); 

        $chapters = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chapters[] = $rec;
        }
         
        return $chapters;
    }
    
    function fetchMangaChaptersUpdate($mangaId, $chapters) {
    	
        $query = "SELECT id_chapter, number, name, chapter_url, release_date, manga_id FROM chapter WHERE manga_id = ? AND number NOT IN (" . $chapters . ") ORDER BY number";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);

        $stmt->execute();

        $chapters = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chapters[] = $rec;
        }

        return $chapters;
    }
    
    function insertChapter($chapter) {

        $query = "INSERT INTO chapter SET number = ?, name = ?, chapter_url = ?, manga_id = ?";

        $stmt = $this->conn->prepare($query);
 
        $stmt->bindParam(1, $chapter["number"]);
        $stmt->bindParam(2, $chapter["name"]);
        $stmt->bindParam(3, $chapter["chapter_url"]);
        $stmt->bindParam(4, $chapter["manga_id"]);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function updateChapterDone($chapterId) {

        $query = "UPDATE chapter SET done = 1 WHERE id_chapter = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $chapterId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function updateChapterName($chapterId, $name) {

        $query = "UPDATE chapter SET name = ? WHERE id_chapter = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $chapterId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}


?>