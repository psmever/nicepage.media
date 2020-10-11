<?php
namespace App\Http;


class BaseController
{
    public function __construct()
	{

    }

    public static function baseTest() : string
    {
        return "baseTest";
    }
}
