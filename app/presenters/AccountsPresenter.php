<?php


class AccountsPresenter extends MainPresenter
{
    public static $isSecurity = false;
    public function getListAccounts() { echo (new Accounts())->getListAccounts(); }
    public function getAccountInfo() { echo (new Accounts())->getAccountInfo(); }
    public function addAccount() { echo (new Accounts())->addAccount(); }
    public function deleteAccount() { echo (new Accounts())->deleteAccount(); }
    public function editAccountStatus() { echo (new Accounts())->editAccountStatus(); }
}