<?php

$router->get('posts/([0-9]+)', 'posts@getPost');

$router->get('posts', 'posts@getPosts');

$router->post('posts/([0-9]+)', 'posts@updatePost');

$router->post('posts', 'posts@addPost');

$router->delete('posts/([0-9]+)', 'posts@deletePost');

$router->get('', function() {
    echo 'REST API for Blog';
});