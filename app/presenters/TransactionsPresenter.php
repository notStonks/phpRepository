<?php


class TransactionsPresenter extends MainPresenter
{
    public static $isSecurity = false;

    public function getListTransactions(){ echo (new Transactions())->getListTransactions(); }
    public function getTransactionInfo(){ echo (new Transactions())->getTransaction(); }
    public function createTransaction(){ echo (new Transactions())->createTransaction(); }
    public function confirmTransaction(){ echo (new Transactions())->confirmTransaction(); }
}