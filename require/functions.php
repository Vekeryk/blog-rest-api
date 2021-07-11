<?php 

function getPosts ($mysql) {
    $posts = $mysql->query("SELECT * FROM `blogs`");

    $postsList = [];

    while($post = mysqli_fetch_assoc($posts)) {
        $postsList[] = $post;
    }

    echo json_encode($postsList, JSON_UNESCAPED_UNICODE);
}

function getPost ($mysql, $id) {
    $post = $mysql->query("SELECT * FROM `blogs` WHERE `id` = '$id'");

    if (mysqli_num_rows($post) === 0) {
        http_response_code(404);
        $res = [
            "status" => false,
            "message" => "Такий пост не знайдено"
        ];

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    } else {
        $post = mysqli_fetch_assoc($post);

        echo json_encode($post, JSON_UNESCAPED_UNICODE);
    }
}

function addPost ($mysql, $data, $file) {
    if(!$data['title']) {
        http_response_code(415);
        $res = [
            "status" => false,
            "message" => "Пост обов' язково має містити заголовок"
        ];
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        return;
    }
    // якщо фото завантажено успішно
    $image = uploadImage($file);
    if($image) {
        $title = $data['title'];
        $body = $data['body'];

        $mysql->query("INSERT INTO `blogs` (`id`, `title`, `body`, `image`) VALUES (NULL, '$title', '$body', '$image')");

        http_response_code(201);

        $res = [
            "status" => true,
            "post_id" => mysqli_insert_id($mysql)
        ];

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}

function uploadImage($file) {
    $fileName  =  $file['image']['name'];
    $tempPath  =  $file['image']['tmp_name'];
    $fileSize  =  $file['image']['size'];

    if(empty($fileName)) {
        http_response_code(415);
        $error = [ "status" => false, "message" => "Додайте зображення до поста" ];
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
                $filePath = $uploadPath . time() . ".$fileExt";
                move_uploaded_file($tempPath, $filePath); // переміщує файл з тимчасового сховища в папку image
            }
            else {
                http_response_code(415);	
                $error = [ "status" => false, "message" => "Величина файлу перевищує 5 MB" ];
            }
        }
        else {
            http_response_code(415);
            $error = [ "status" => false, "message" => "Дозволено тільки файли з розширенням JPG, JPEG та PNG" ];
        }
    }

    if(isset($error)) {
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        return False;
    }
    else {
        return $filePath;
    }
}

function updatePost($mysql, $id, $data, $file) {
    $title = $data['title'];
    $body = $data['body'];

    if(empty($_FILES)) {
        $image = $data['image'];
        $mysql->query("UPDATE `blogs` SET `title` = '$title', `body` = '$body', `image` = '$image' WHERE `id` = '$id'");
    }
    else {
        $image = uploadImage($file);
        if($image) {
            $mysql->query("UPDATE `blogs` SET `title` = '$title', `body` = '$body', `image` = '$image' WHERE `id` = '$id'");
        }
        else {
            return;
        }
    }
    http_response_code(200);
    $res = [
        "status" => true,
        "message" => "Пост оновлено"
    ];
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
}

function deletePost($mysql, $id) {
    deleteImage($mysql, $id);

    $mysql->query("DELETE FROM `blogs` WHERE `blogs`.`id` = '$id'");

    http_response_code(200);

    $res = [
        "status" => true,
        "message" => "Пост видалено"
    ];

    echo json_encode($res, JSON_UNESCAPED_UNICODE);
}

function deleteImage($mysql, $id) {
    $post = $mysql->query("SELECT * FROM `blogs` WHERE `id` = '$id'");
    $post = mysqli_fetch_assoc($post);
    unlink($post["image"]);
}