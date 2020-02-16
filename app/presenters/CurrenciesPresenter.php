<?php

class CurrenciesPresenter extends MainPresenter {

    public static $isSecurity = false;

    public function getListCurrencies(){ echo (new Currencies())->getListCurrencies(); }
    public function getCurrencyInfo(){echo(new Currencies())->getCurrencyInfo();}
    



}

?>