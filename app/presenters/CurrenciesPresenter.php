<?php

class CurrenciesPresenter extends MainPresenter {

    public static $isSecurity = false;

    public function getListCurrencies(){ echo (new Currencies())->getListCurrencies(); }
    public function getCurrencyInfo(){echo(new Currencies())->getCurrency();}
    public function getUserInfo(){echo (new Currencies())->getUserInfo();}
    public function addUser(){echo (new Currencies())->addUser();}
    public function deleteUser(){echo (new Currencies())->deleteUser();}
    public function editUser(){echo (new Currencies())->editUser();}
    public function getListCards(){echo (new Currencies())->getListCards();}
    public function editUserCardStatus(){echo (new Currencies())->editUserCardStatus();}



}

?>