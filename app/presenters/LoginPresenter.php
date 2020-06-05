<?php

class LoginPresenter extends MainPresenter
{

    public static $isSecurity = false;

    public function Login(){ echo (new LoginSys())->Login();}
    public function Register(){ echo (new LoginSys())->Register();}
}

?>