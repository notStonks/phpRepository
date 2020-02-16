<?php 

class UsersPresenter extends MainPresenter {

	public static $isSecurity = false;

	public function getListUsers(){ echo (new Users())->getListUsers(); }
	public function getUserInfo(){echo (new Users())->getUserInfo();}
	public function addUser(){echo (new Users())->addUser();}
	public function deleteUser(){echo (new Users())->deleteUser();}
	public function editUser(){echo (new Users())->editUser();}
	public function getListCards(){echo (new Users())->getListCards();}
	public function editUserCardStatus(){echo (new Users())->editUserCardStatus();}



}

?>