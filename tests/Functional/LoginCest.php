<?php

class LoginCest
{
    public function _before(FunctionalTester $I)
    {
        // 在每個測試前執行
    }

    public function loginSuccessfully(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'correct_password');
        $I->click('登入');
        $I->see('歡迎回來');
    }

    public function loginWithInvalidPassword(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'wrong_password');
        $I->click('登入');
        $I->see('帳號或密碼錯誤');
    }
}