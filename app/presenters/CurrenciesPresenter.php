<?php

class CurrenciesPresenter extends MainPresenter {

    public static $isSecurity = false;

    public function getListCurrencies(){ echo (new Currencies())->getListCurrencies(); }
    public function getCurrencyInfo(){echo(new Currencies())->getCurrencyInfo();}
    public function addCurrency(){echo(new Currencies())->addCurrency();}
    public function editCurrencyStatus(){echo(new Currencies())->editCurrencyStatus();}
    public function editCurrency(){echo(new Currencies())->editCurrency();}

}

?>