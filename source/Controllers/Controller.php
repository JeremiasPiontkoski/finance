<?php
namespace Source\Controllers;

class Controller 
{
    public array $data;

    public function __construct()
    {
        $this->data = getDataFromInput();
    }
}