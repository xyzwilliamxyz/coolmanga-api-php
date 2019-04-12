<?php
    
class GenreDAO {
	
    public $conn;

    function __construct($conn) {
    	
    	$this->conn = $conn;
    }
    
    function fetchAllGenres() {
    	    
        $query = "SELECT id_genre, description FROM genre";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        $genres = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $genres[] = $rec;
        }
         
        return $genres;
    }
}

?>