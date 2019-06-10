<?php

namespace tests\unit\models;

use app\models\Usuarios;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($user = Usuarios::findIdentity(1));
        expect($user->username)->equals('pepe');

        expect_not(Usuarios::findIdentity(999));
    }

    public function testFindUserByUsername()
    {
        expect_that($user = Usuarios::findByUsername('pepe'));
        expect_not(Usuarios::findByUsername('not-admin'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser($user)
    {
        $user = Usuarios::findByUsername('pepe');

        expect_that($user->validatePassword('pepe'));
        expect_not($user->validatePassword('123456'));        
    }

}
