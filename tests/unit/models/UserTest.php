<?php

namespace tests\unit\models;

use app\models\Usuarios;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($user = Usuarios::findIdentity(100));
        expect($user->username)->equals('admin');

        expect_not(Usuarios::findIdentity(999));
    }

    public function testFindUserByUsername()
    {
        expect_that($user = Usuarios::findByUsername('admin'));
        expect_not(Usuarios::findByUsername('not-admin'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser($user)
    {
        $user = Usuarios::findByUsername('vaca.roberto');

        expect_that($user->validatePassword('$2y$13$AbFTOYb9VlhAT9B5HsTIh.EyojYnHPlXkJBB/ifYg6F/sHa/9SvKS'));
        expect_not($user->validatePassword('123456'));        
    }

}
