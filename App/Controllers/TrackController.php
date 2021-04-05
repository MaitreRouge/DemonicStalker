<?php
namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;

class TestController {

    public function index()
    {
        return new HtmlResponse("Hellow world");
    }

}