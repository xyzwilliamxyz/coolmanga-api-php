<?php
    
class MangaDAO {
	
    public $conn;

    
    function __construct($conn) {
    	
    	$this->conn = $conn;
    }
    
    function fetchAllMangas() {
    	
        $query = "SELECT m.id_manga, m.name, m.cover_url, m.source_id, m.rank FROM manga m";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        $mangas = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mangas[] = $rec;
        }
         
        return $mangas;
    }
	
	function fetchAllMangasFull() {
    	
        $query = "SELECT m.id_manga, m.name, m.cover_url, m.source_id, m.manga_url, m.name FROM manga m";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        $mangas = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mangas[] = $rec;
        }
         
        return $mangas;
    }

    function fetchNewMangas($lastMangaID) {
        
        $query = "SELECT m.id_manga, m.name, m.cover_url, m.source_id, m.rank FROM manga m WHERE m.id_manga > ?";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $lastMangaID, PDO::PARAM_INT);
        $stmt->execute();

        $mangas = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mangas[] = $rec;
        }
        
        return $mangas;
    }



    function fetchMangaGenre($mangaId) {
    
        $query = "SELECT manga_id, genre_id FROM manga_genre WHERE manga_id = ?";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        $mangaGenres = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mangaGenres[] = $rec;
        }
         
        return $mangaGenres;
    }

    function fetchMangaGenres($mangasId) {

        $query = "SELECT manga_id, genre_id FROM manga_genre WHERE manga_id IN (" + $mangasId + ")";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        $mangaGenres = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mangaGenres[] = $rec;
        }
         
        return $mangaGenres;
    }

    function fetchMangaDetails($mangaId, $deviceUid) {

        $query = "SELECT m.id_manga, m.name, m.alternate_name, m.chapters, m.author, m.artist, m.status, m.rank, m.read_direction,
            m.year_release, m.description, m.cover_url, m.source_id, m.last_update, m.score, m.votes FROM manga m WHERE m.id_manga = ?";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        $manga = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$rating = $this->fetchMangaRate($mangaId, $deviceUid);
		
		if ($rating != null) {
			$manga['user_rate'] = $rating['rate'];
		} else {
			$manga['user_rate'] = 0;
		}
         
        return $manga;
    }

    function fetchMangaDetailsByName($mangaName) {

        $query = "SELECT m.id_manga, m.name, m.alternate_name, m.chapters, m.author, m.artist, m.status, m.rank, m.read_direction,
            m.year_release, m.description, m.cover_url, m.source_id, m.last_update, m.score, m.votes FROM manga m WHERE m.name = ?";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaName, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $manga = $stmt->fetch(PDO::FETCH_ASSOC);
			
			
        } else {
            $manga = null;
        }
         
        return $manga;
        
    }

    function insertManga($manga) {

        $query = "INSERT INTO manga SET name = ?, alternate_name = ?, chapters = ?, author = ?, artist = ?, status = ?, rank = ?,
                    read_direction = ?, year_release = ?, description = ?, cover_url = ?, source_id = ?, last_update = ?, manga_url = ?";
                    
        $stmt = $this->conn->prepare($query);
 
        $stmt->bindParam(1, $manga['name']);
        $stmt->bindParam(2, $manga['alternate_name']);
        $stmt->bindParam(3, $manga['chapters']);
        $stmt->bindParam(4, $manga['author']);
        $stmt->bindParam(5, $manga['artist']);
        $stmt->bindParam(6, $manga['status']);
		$rank = 9999999; // no rank
        $stmt->bindParam(7, $rank);
        $stmt->bindParam(8, $manga['read_direction']);
        $stmt->bindParam(9, $manga['year_release']);
        $stmt->bindParam(10, $manga['description']);
        $stmt->bindParam(11, $manga['cover_url']);
        $a = 0;
        $stmt->bindParam(12, $a);//$manga['source_id']);
        $stmt->bindParam(13, $manga['last_update']);
        $stmt->bindParam(14, $manga['manga_url']);
  
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function insertMangaGenre($mangaId, $genreId) {

        $query = "INSERT INTO manga_genre SET manga_id = ?, genre_id = ?";

        $stmt = $this->conn->prepare($query);
 
        $stmt->bindParam(1, $mangaId);
        $stmt->bindParam(2, $genreId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
	
	function fetchMangaRate($mangaId, $deviceId) {
		
		$query = "SELECT * FROM rating WHERE manga_id = ? AND device_uid = ?";
		
		$stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $mangaId, PDO::PARAM_INT);
		$stmt->bindParam(2, $deviceId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $rating = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $rating = null;
        }
         
        return $rating;
	}
	
	function insertOrUpdateMangaRate($mangaId, $deviceUid, $rate) {
		
		$existing_rating = $this->fetchMangaRate($mangaId, $deviceUid);
		
		if ($existing_rating == null) {
			$sql = "INSERT INTO rating SET rate = ?, device_uid = ?, manga_id = ?";
		} else {
			$sql = "UPDATE rating SET rate = ?, device_uid = ?, manga_id = ? WHERE id_rating = ?";
		}
		
		$stmt = $this->conn->prepare($sql);
		
		$stmt->bindParam(1, $rate);
        $stmt->bindParam(2, $deviceUid);
		$stmt->bindParam(3, $mangaId);
		
		if ($existing_rating != null) {
			$stmt->bindParam(4, $existing_rating['id_rating']);
		}

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
	}
	
	function updateScores() {
		
		// reset rank variable
		$stmt = $this->conn->prepare("SET @rank = 0;");
		$stmt->execute();
		
		$sql = "UPDATE manga m 
				JOIN (SELECT manga_id, TRUNCATE(SUM(rate) / COUNT(*), 1) AS score, COUNT(*) AS votes
						FROM rating
						GROUP BY manga_id
						ORDER BY score DESC) r
				ON r.manga_id = m.id_manga
				SET m.score = r.score, m.votes = r.votes, m.rank = @rank := @rank + 1";
				
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->rowCount();
	}
    
    function fetchAllMangaGenres() {
        // manga_genre
        $query = "SELECT manga_id, genre_id FROM manga_genre";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        $res = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $rec;
        }
        
        return $res;
    }

    function fetchAllMangaRank() {
        // manga_genre
        $query = "SELECT id_manga, rank FROM manga";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        $res = array();
        while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $res[] = $rec;
        }
        
        return $res;
    }
	
}


?>