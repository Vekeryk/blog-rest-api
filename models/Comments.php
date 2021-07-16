<?php

class Comments {

    private $database;

    public function __construct() {
        $this->database = new Database();
    }
    
    public function getComments($post_id) {
        $query = "SELECT * FROM `comments` WHERE `post_id` = '$post_id'";
        $stmt = $this->database->run($query);

        $commentsList = [];
    
        while($comment = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commentsList[] = $comment;
        }

        return $commentsList;
    }

    public function addComment($post_id) {
            $body = $_POST['body'];
    
            $query = "INSERT INTO `comments` (`id`, `user_id`, `post_id`, `body`) VALUES (NULL, 1, '$post_id', '$body')";
            $this->database->run($query);

            http_response_code(201);
            return [ "status" => true, "comment_id" => $this->database->conn->lastInsertId() ];
    }
}