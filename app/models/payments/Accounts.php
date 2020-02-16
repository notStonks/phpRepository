<?php 

class Accounts{

	public function getListAccounts(){
		$res = _MainModel::table("accounts")->get()->send();

		_MainModel::viewJSON($res);
	}
}