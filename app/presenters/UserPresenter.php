<?php 

class UserPresenter extends MainPresenter {

	public static $isSecurity = false;

	public function getListUsers(){ echo (new Users())->getListUsers(); }
	public function getUserInfo(){echo (new Users())->getUserInfo(self::$params_url['id']);}
}

?>