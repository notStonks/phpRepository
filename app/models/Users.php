<?php
 
class Users extends MainModel{


    public function getListUsers(){
        $result = _MainModel::table("users")->get()->send();
        _MainModel::viewJSON($result);

         //(new Accounts())->getListAccounts();
    }

    public function getUserInfo(){
        /*$id = _MainModel::$params_url['id'];
        $result = _MainModel::table("users")->get()->filter(array("id"=> $id))->send();
        _MainModel::viewJSON($result);*/
        foreach (self::$params_url as $parm){
        echo "</br> ".$parm;}
    }

}

?>