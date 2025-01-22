<?php
namespace Source\Controllers;

use Source\Support\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert(): ?array
    {
        $validator = new Validator($this->data);
        $validator
            ->required("name")
            ->required("email")
            ->email("email")
            ->required("password")
            ->min("password", 6);

        if ($validator->fails()) {
            echo json_encode($validator->getErrors());
        }
        

        return null;
    }
}