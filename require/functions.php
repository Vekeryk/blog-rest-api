<?php 

function getPosts ($mysql) {
    $posts = $mysql->query("SELECT * FROM `blogs`");

    $postsList = [];

    while($post = mysqli_fetch_assoc($posts)) {
        $postsList[] = $post;
    }

    echo json_encode($postsList);
}

function getPost ($mysql, $id) {
    $post = $mysql->query("SELECT * FROM `blogs` WHERE `id` = '$id'");

    if (mysqli_num_rows($post) === 0) {
        http_response_code(404);
        $res = [
            "status" => false,
            "message" => "Такий пост не знайдено"
        ];

        echo json_encode($res);
    } else {
        $post = mysqli_fetch_assoc($post);

        echo json_encode($post);
    }
}

function addPost ($mysql, $data, $file) {
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

        echo json_encode($res);
    }
}

function uploadImage($file) {
    $fileName  =  $file['image']['name'];
    $tempPath  =  $file['image']['tmp_name'];
    $fileSize  =  $file['image']['size'];
            
    if(empty($fileName)) {
        $error = [ "message" => "Додайте зображення до поста", "status" => false ];
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
                $error = [ "message" => "Величина файлу перевищує 5 MB", "status" => false ];
            }
        }
        else {		
            $error = [ "message" => "Дозволено тільки файли з розширенням JPG, JPEG та PNG", "status" => false ];
        }
    }

    if(isset($error)) {
        echo json_encode($error);
        return False;
    }
    else {
        return $filePath;
    }
}

function updatePost($mysql, $id, $data) {
    $title = $data['title'];
    $body = $data['body'];
    $image = $data['image'];

    $mysql->query("UPDATE `blogs` SET `title` = '$title', `body` = '$body', `image` = '$image' WHERE `blogs`.`id` = '$id'");

    http_response_code(200);

    $res = [
        "status" => true,
        "message" => "Пост оновлено"
    ];

    echo json_encode($res);
}

function deletePost($mysql, $id) {
    $mysql->query("DELETE FROM `blogs` WHERE `blogs`.`id` = '$id'");

    http_response_code(200);

    $res = [
        "status" => true,
        "message" => "Пост видалено"
    ];

    echo json_encode($res);
}