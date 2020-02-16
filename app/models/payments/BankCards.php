<?php

class BankCards {
    private $table= "bank_cards";


    /*public function getListCards(){
        $id = _MainModel::$params_url['id'];
        $result = _MainModel::table($this->table)->get()->send();
        _MainModel::viewJSON($result);

    }

    public function getUserInfo(){

        $id = _MainModel::$params_url['id'];
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> $id))->send();
        _MainModel::viewJSON($result);

    }
    public function addUser(){
        $nickname = _MainModel::$params_url['nickname'];
        //$status = _MainModel::$params_url['status'];
        $status = "unblocked";
        $res = _MainModel::table($this->table)->add(array("nickname" => $nickname, "status" => $status))->send();
        _MainModel::viewJSON($res);
    }
    public function deleteUser(){
        $id = _MainModel::$params_url['id'];
        $result = _MainModel::table($this->table)->delete(array("id"=> $id))->send();
        _MainModel::viewJSON($result);
    }

    public function editUser(){
        $id = _MainModel::$params_url['id'];
        $nickname = _MainModel::$params_url['nickname'];
        $status = _MainModel::$params_url['status'];
        _MainModel::table($this->table)->edit(array("nickname" => $nickname, "status" => $status), array("id" => $id))->send();
    }*/
}

?>