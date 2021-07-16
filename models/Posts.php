<?php

class Posts {

    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    public function getPosts() {
        $query = "SELECT * FROM `blogs`";
        $stmt = $this->database->run($query);

        $postsList = [];
    
        while($post = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $postsList[] = $post;
        }

        return $postsList;
    }

    public function getPost($id) {
        $post = $this->postExist($id);
        if ($post) {
            return $post;
        } else {
            http_response_code(404);
            return [ "status" => false, "message" => "Пост не знайдено" ];
        }
    }

    private function postExist($id) {
        $query = "SELECT * FROM `blogs` WHERE `id` = '$id'";
        $stmt = $this->database->run($query);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $post;
    }

    public function addPost() {
        if (!$_POST['title']) {
            return [ 
                "status" => false, 
                "message" => "Пост обов'язково має містити заголовок"
            ];
        };

        $image = $this->uploadImage($_FILES);
        // якщо фото завантажено успішно
        if (!is_string($image)) {
            return $image;
        } 
        else {
            $title = $_POST['title'];
            $body = $_POST['body'];
    
            $query = "INSERT INTO `blogs` (`id`, `title`, `body`, `image`) VALUES (NULL, '$title', '$body', '$image')";
            $this->database->run($query);

            http_response_code(201);
            return [ "status" => true, "post_id" => $this->database->conn->lastInsertId() ];
        }
    }

    private function uploadImage() {
        $file_name  =  $_FILES['image']['name'];
        $temp_path  =  $_FILES['image']['tmp_name'];
        $file_size  =  $_FILES['image']['size'];
        $uploadPath = 'images/';

        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION); // отримання розширення

        $error = $this->validateImage($file_name, $file_ext, $file_size);
        if (isset($error)) {
            return $error;
        }

        $path = $uploadPath . time() . ".$file_ext";
        move_uploaded_file($temp_path, $path); // переміщує файл з тимчасового сховища в папку image
        return $path;
    }

    private function validateImage($file_name, $file_ext, $file_size) {
        $valid_extensions = ['jpeg', 'jpg', 'png']; // список допустимих розширень

        if(empty($file_name)) {
            http_response_code(415);
            return [ "status" => false, "message" => "Додайте зображення до поста" ];
        }
        // Дозволити файли тільки з валідними розширеннями
        if(!in_array($file_ext, $valid_extensions)) {
            http_response_code(415);
            return [ "status" => false, "message" => "Дозволено тільки файли з розширенням JPG, JPEG та PNG" ];
        }
        // Перевіряє розмір файлу '5MB'
        if($file_size > 5000000) {
            http_response_code(415);	
            return [ "status" => false, "message" => "Величина файлу перевищує 5 MB" ];
        }
    }

    public function updatePost($id) {
        $title = $_POST['title'];
        $body = $_POST['body'];

        if (!$title) {
            return [ 
                "status" => false, 
                "message" => "Пост обов'язково має містити заголовок"
            ];
        }

        $query = $this->updateImage($id, $title, $body);

        $this->database->run($query);
        http_response_code(200);
        return [ "status" => true, "message" => "Пост оновлено" ];
    }

    private function updateImage($id, $title, $body) {
        if(empty($_FILES)) {
            $image = $_POST['image'];
            $query = "UPDATE `blogs` SET `title` = '$title', `body` = '$body', `image` = '$image' WHERE `id` = '$id'";
        } else {
            $image = $this->uploadImage();

            if(!is_string($image)) {
                return $image;
            }
            else {
                $query = "UPDATE `blogs` SET `title` = '$title', `body` = '$body', `image` = '$image' WHERE `id` = '$id'";
            }
        }
        return $query;
    }

    public function deletePost($id) {
        $post = $this->postExist($id);

        if($post) {
            $query = "DELETE FROM `blogs` WHERE `blogs`.`id` = '$id'";
            $this->database->run($query);
    
            if (file_exists($post["image"]))
                unlink($post["image"]);
    
            http_response_code(200);
            return [ "status" => true, "message" => "Пост видалено" ];

        } else {
            return  [ "status" => false, "message" => "Пост не знайдено" ];
        }
    }
}