<?php
    
class CommentDAO {
	
    public $conn;
    
    function __construct($conn) {
    	
    	$this->conn = $conn;
    }
	
	function fetchLastCommentNumberByMangaId($mangaId) {
    	
        $query = "SELECT MAX(number) AS number FROM comment WHERE manga_id = ? ORDER BY number DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res["number"];
    }
    
    function fetchRecentComments($mangaId, $number) {
    	
        $query = "SELECT id_comment, number, text, author, datetime, manga_id FROM comment WHERE manga_id = ? and number > ? ORDER BY number DESC limit 10";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
		$stmt->bindParam(2, $number, PDO::PARAM_INT);
        $stmt->execute(); 

        $comments = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $rec;
        }
         
        return $comments;
    }
	
	function fetchCommentById($idComment) {
    	
        $query = "SELECT id_comment, number, text, author, datetime, manga_id FROM comment WHERE id_comment = ?";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $idComment, PDO::PARAM_INT);
        $stmt->execute(); 

        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
         
        return $rec;
    }
    
    function fetchOldComments($mangaId, $number) {
    	
        $query = "SELECT id_comment, number, text, author, datetime, manga_id FROM comment WHERE manga_id = ? and number < ? ORDER BY number DESC limit 10";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
		$stmt->bindParam(2, $number, PDO::PARAM_INT);
        $stmt->execute();

        $comments = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $rec;
        }
        
        return $comments;
    }
    
    function insertComment($comment) {

        $query = "INSERT INTO comment SET number = ?, text = ?, author = ?, datetime = now(), manga_id = ?";

        $stmt = $this->conn->prepare($query);
		
		$number = $this->fetchLastCommentNumberByMangaId($comment["manga_id"]) + 1;
 
        $stmt->bindParam(1, $number);
        $stmt->bindParam(2, $comment["text"]);
        $stmt->bindParam(3, $comment["author"]);
        //$stmt->bindParam(4, $comment["datetime"]);
		$stmt->bindParam(4, $comment["manga_id"]);

        if ($stmt->execute()) {
			
			$lastId = $this->conn->lastInsertId();
			$response = $this->fetchCommentById($lastId);
			
            return $response;
        } else {
            return array();
        }
    }

    function updateComment($comment) {

        $query = "UPDATE comment SET number = ?, text = ?, author = ?, datetime = ?, manga_id = ? WHERE id_comment = ?";

        $stmt = $this->conn->prepare($query);
 
        $stmt->bindParam(1, $comment["number"]);
        $stmt->bindParam(2, $comment["text"]);
        $stmt->bindParam(3, $comment["author"]);
        $stmt->bindParam(4, $comment["datetime"]);
		$stmt->bindParam(5, $comment["manga_id"]);
		$stmt->bindParam(6, $comment["idComment"]);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}


?>