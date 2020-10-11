<?php
namespace App\Http;

use App\Http\BaseController;

class ImageController extends BaseController
{
    public function __construct()
	{

    }

    public static function test() : string
    {
        return "baseTest";
    }

    public static function start()
    {
        echo "start";
    }
}
