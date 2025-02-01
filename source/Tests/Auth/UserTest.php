<?php
namespace Source\Tests\Auth;

use Source\Controllers\UserController;
use Source\Tests\Test;

class UserTest extends Test
{
    public function testInsertUser()
    {
        $user = new UserController();
        $user->data = [
            "name" => "email9",
            "email" => "email9@gmail.com",
            "password" => "123456"
        ];
        ob_start();
        $user->insert();
        $output = json_decode(ob_get_clean(), true);
        $this->assertEquals(201, $output['statusCode']);
    }
}
