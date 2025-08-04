<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
    private $authController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController();
    }

    public function testLoginWithValidCredentials()
    {
        // 測試有效的登入憑證
        $result = $this->authController->validateCredentials('admin', 'correct_password');
        $this->assertTrue($result);
    }

    public function testLoginWithInvalidCredentials()
    {
        // 測試無效的登入憑證
        $result = $this->authController->validateCredentials('admin', 'wrong_password');
        $this->assertFalse($result);
    }
}