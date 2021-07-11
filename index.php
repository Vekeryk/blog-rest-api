<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *, Authorization');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header("Content-type: application/json");

//підключення бази даних
require "require/connect.php";
//всі функції виведені в окремий файл
require "require/functions.php";

$method = $_SERVER['REQUEST_METHOD'];

$link = $_GET['q'];
//розбиваємо посилання
$params = explode('/', $link);

$type = $params[0];
$id = $params[1];

switch ($method) {
    case "GET":
        //запити спрацьовують тільки за посиланням /posts
        if ($type === 'posts') {
            if (isset($id)) {
                getPost($mysql, $id);
            } else {
                getPosts($mysql);
            }
        }
        break;
    case "POST":
        if ($type === 'posts') {
            addPost($mysql, $_POST, $_FILES);
        }
        break;
    case "PATCH":
        if ($type === 'posts') {
            if (isset($id)) {
                // приймаємо json
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                updatePost($mysql, $id, $data, $_FILES);
            }
        }
        break;
    case "DELETE":
        if ($type === 'posts') {
            if (isset($id)) {
                deletePost($mysql, $id);
            }
        }
        break;
}

