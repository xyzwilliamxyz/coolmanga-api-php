<?php
    
class PageDAO {
	
    public $conn;
    
    function __construct($conn) {
    	
    	$this->conn = $conn;
    }
    
    function fetchChapterPagesCount($chapterId) {
    	
        $query = "SELECT COUNT(id_page) FROM page WHERE chapter_id = ?";
        $stmt = $conn->prepare( $query  );
        $stmt->bindParam(1, $chapterId, PDO::PARAM_INT);
        $stmt->execute();

        $count = $stmt->fetch(PDO::FETCH_ASSOC);

        return $count;
    }
    
    function fetchChapterPages($chapterId) {
    	
        $query = "SELECT id_page, chapter_id, number, page_url FROM page WHERE chapter_id = ?";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $chapterId, PDO::PARAM_INT);
        $stmt->execute();

        $pages = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pages[] = $rec;
        }
         
        return $pages;
    }
    
    function fetchChapterPagesUpdate($chapterId, $pageCount) {
    	
        $query = "SELECT id_page, chapter_id, number, page_url FROM page WHERE chapter_id = ? AND number > ?";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $chapterId, PDO::PARAM_INT);
        $stmt->bindParam(2, $pageCount, PDO::PARAM_INT);

        $stmt->execute();

        $pages = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pages[] = $rec;
        }

        return $pages;
    }

    function fetchChaptersPages($chapters) {
        
        $query = "SELECT p.id_page, p.chapter_id, p.number, p.page_url FROM page p JOIN chapter c ON p.chapter_id = c.id_chapter WHERE p.chapter_id IN (" . $chapters . ") AND c.done = 1";
        $stmt = $this->conn->prepare( $query );

        $stmt->execute();

        $pages = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pages[] = $rec;
        }

        return $pages;
    }

    function insertPage($page) {

        $query = "INSERT INTO page SET number = ?, page_url = ?, chapter_id = ?";

        $stmt = $this->conn->prepare($query);
 
        $stmt->bindParam(1, $page["number"]);
        $stmt->bindParam(2, $page["page_url"]);
        $stmt->bindParam(3, $page["chapter_id"]);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function updatePageURL($page) {

        $query = "UPDATE page SET done = 1, page_url = ? WHERE id_page = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $page['page_url']);
        $stmt->bindParam(2, $page['id_page']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function fetchOldPages($chapterId) {
        
        $query = "SELECT p.id_page, p.chapter_id, p.number, p.page_url, c.chapter_url FROM page p JOIN chapter c ON c.id_chapter = ? AND p.chapter_id = c.id_chapter WHERE p.done = 0";
        $stmt = $this->conn->prepare( $query );

        $stmt->bindParam(1, $chapterId);
        
        $stmt->execute();

        $pages = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pages[] = $rec;
        }

        return $pages;
    }
    
}


?>