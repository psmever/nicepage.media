<?php
use App\Route\Route;
use App\Http\ImageController;


require __DIR__ .'/../vendor/autoload.php';

define('BASEPATH','/');

Route::add('/phpinfo', function() {
    phpinfo();
});

Route::add('/image-upload', function() {
    ImageController::start();
}, 'post');










Route::run(BASEPATH);
?>