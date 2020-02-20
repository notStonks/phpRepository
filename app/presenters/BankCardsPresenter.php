<?php

class BankCardPresenter extends MainPresenter {

    public static $isSecurity = false;

    public function getListCards(){ echo (new BankCards())->getListCards(); }
    public function addCard(){echo (new BankCards())->addCard();}
    public function deleteCard(){echo (new BankCards())->deleteCard();}
    public function getCardInfo(){echo (new BankCards())->getCardInfo();}
    public function editCardStatus(){ echo (new BankCards())->editCardStatus(); }

}

?>