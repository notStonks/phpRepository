<?php
 
class Test {


    public function getListUsers(){
        $result = _MainModel::table("users")->get()->send();
        
        _MainModel::viewJSON($result);   
    }

}

?>