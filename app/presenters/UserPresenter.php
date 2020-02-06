<?php 

class UserPresenter extends MainPresenter {

	public static $isSecurity = false;

	public function getListUsers(){ echo (new Users())->getListUsers(); }
	public function getUserInfo($id){echo (new Users())->getUserInfo($id);}
}

?>