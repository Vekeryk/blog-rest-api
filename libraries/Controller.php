<?php
    // Клас, що підключає моделі
    class Controller {
        public function model($model) {
            require_once 'models/' . $model . '.php';
            return new $model();
        }
    }