<?php


class CurrencyRatesPresenter
{
    public static $isSecurity = false;

    public function getListCurrencyRates(){ echo (new CurrencyRates())->getListCurrencyRates(); }
    public function getCurrencyRateInfo(){ echo (new CurrencyRates())->getCurrencyRateInfo(); }
    public function addCurrencyRate(){ echo (new CurrencyRates())->addCurrencyRate(); }
    public function updRate(){ echo (new CurrencyRates())->updRate(); }
    public function updRateCB(){ echo (new CurrencyRates())->updRateCB(); }

}