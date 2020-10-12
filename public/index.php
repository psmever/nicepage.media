<?php
use App\Route\Route;
use App\Http\ImageController;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/Bootstrap.php';

Route::add('/phpinfo', function() {
    phpinfo();
});

Route::add('/image-upload', function() {
    $task = new ImageController();

    $task::start();

}, 'post');

Route::run(BASEPATH);
?>