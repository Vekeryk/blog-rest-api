<?php

class CommentsController extends Controller
{
    public function __construct() {
        $this->commentsModel = $this->model('Comments');
    }

    public function getComments($post_id) {
        $response = $this->commentsModel->getComments($post_id);
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function addComment($post_id) {
        $response = $this->commentsModel->addComment($post_id);
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}