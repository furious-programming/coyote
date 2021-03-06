<?php

use Coyote\User;

class UserTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testUserRegistration()
    {
        $name = 'Joe Doe';
        $email = 'johndoe@example.com';
        $password = bcrypt('password');

        User::create(['name' => $name, 'email' => $email, 'password' => $password]);

        $this->tester->seeRecord('users', ['name' => $name, 'email' => $email, 'password' => $password]);
    }

    public function testIfModelReturnsAdminUserEmail()
    {
        // access model
        $user = User::where('name', 'admin')->first();
        $this->assertEquals('admin@4programmers.net', $user->email);
    }

    public function testSettingsSaving()
    {
        $user = User::where('name', 'admin')->first();
        $user->setSetting('foo', 'bar');

        $this->assertEquals('{"foo":"bar"}', $user->settings);
        $this->assertEquals('bar', $user->getSetting('foo'));
    }

    public function testSettingsDefault()
    {
        $user = User::where('name', 'admin')->first();
        $this->assertEquals('Lorem ipsum', $user->getSetting('non existing setting', 'Lorem ipsum'));
    }
}