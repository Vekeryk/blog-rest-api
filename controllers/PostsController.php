<?php

class PostsController extends Controller
{
    public function __construct() {
        $this->postModel = $this->model('Posts');
    }

    public function getPosts() {
        $response = $this->postModel->getPosts();
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getPost($id) {
        $response = $this->postModel->getPost($id);
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function addPost() {
        $response = $this->postModel->addPost();
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function updatePost($id) {
        $response = $this->postModel->updatePost($id);
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function deletePost($id) {
        $response = $this->postModel->deletePost($id);
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}