<?php

class Posts {
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    public function getPosts() 
    {
        $query = "SELECT * FROM `blogs`";
        $stmt = $this->database->run($query);

        $postsList = [];
    
        while($post = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $postsList[] = $post;
        }

        return $postsList;
    }

    public function getPost($id) 
    {
        $query = "SELECT * FROM `blogs` WHERE `id` = '$id'";
        $stmt = $this->database->run($query);

        $num_rows = $stmt->rowCount();

        if ($num_rows === 0) {
            http_response_code(404);
            $post = [
                "status" => false,
                "message" => "Такий пост не знайдено"
            ];
        } else {
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $post;
    }

    public function addPost() 
    {
        if (!$_POST['title']) {
            $error = [
                "status" => false,
                "message" => "Пост обов' язково має містити заголовок"
            ];
            return $error;
        };

        // якщо фото завантажено успішно
        $image = $this->uploadImage($_FILES);

        if (!is_string($image)) {
            return $image;
        } 
        else {
            $title = $_POST['title'];
            $body = $_POST['body'];
    
            $query = "INSERT INTO `blogs` (`id`, `title`, `body`, `image`) VALUES (NULL, '$title', '$body', '$image')";
            $this->database->run($query);

            http_response_code(201);
            $result = [
                "status" => true,
                "post_id" => $this->database->conn->lastInsertId()
            ];
        }

        return $result;
    }

    private function uploadImage($file) {
        $fileName  =  $file['image']['name'];
        $tempPath  =  $file['image']['tmp_name'];
        $fileSize  =  $file['image']['size'];
    
        if(empty($fileName)) {
            http_response_code(415);
            $result = [ "status" => false, "message" => "Додайте зображення до поста" ];
        }
        else {
            $uploadPath = 'images/';
            
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION); // отримання розширення
                
            // список допустимих розширень
            $valid_extensions = array('jpeg', 'jpg', 'png'); 
                            
            // дозволити файли тільки з валідними розширеннями
            if(in_array($fileExt, $valid_extensions)) {				
                // Перевіряє розмір файлу '5MB'
                if($fileSize < 5000000) {
                    $result = $uploadPath . time() . ".$fileExt";
                    move_uploaded_file($tempPath, $result); // переміщує файл з тимчасового сховища в папку image
                }
                else {
                    http_response_code(415);	
                    $result = [ "status" => false, "message" => "Величина файлу перевищує 5 MB" ];
                }
            }
            else {
                http_response_code(415);
                $result = [ "status" => false, "message" => "Дозволено тільки файли з розширенням JPG, JPEG та PNG" ];
            }
        }
    
        return $result;
    }

    public function deletePost($id) {
        $query = "SELECT * FROM `blogs` WHERE `id` = '$id'";
        $stmt = $this->database->run($query);

        if ($stmt->rowCount() === 0) {
            $result = [
                "status" => false,
                "message" => "Пост не знайдено"
            ];
        }
        else {
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            unlink($post["image"]);

            $query = "DELETE FROM `blogs` WHERE `blogs`.`id` = '$id'";
            $this->database->run($query);

            http_response_code(200);
            $result = [
                "status" => true,
                "message" => "Пост видалено"
            ];
        }
        return $result;
    }
}