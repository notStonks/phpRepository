<?php
 
class Users extends _MainModel{


    public function getListUsers(){
        $result = _MainModel::table("users")->get()->send();
        _MainModel::viewJSON($result);

         //(new Accounts())->getListAccounts();
    }

    public function getUserInfo(){
        $id = self::$params_url['id'];
        $result = _MainModel::table("users")->get()->filter(array("id"=> $id))->send();
        _MainModel::viewJSON($result);
    }

}

?>