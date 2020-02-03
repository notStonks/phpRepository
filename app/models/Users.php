<?php
 
class Users {


    public function getListUsers(){
        $result = _MainModel::table("users_cards")->get()->send();
        
        _MainModel::viewJSON($result);   
    }

}

?>