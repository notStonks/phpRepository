<?php 

class UserPresenter extends MainPresenter {

	public static $isSecurity = false;

	public function getListUser(){ echo (new Users())->getListUsers(); }
	
}

?>