<?php
 
class Users {
    private $table= "users";
    private $bank = "bank_cards";


    public function getListUsers(){
        $result = _MainModel::table($this->table)->get()->send();
        _MainModel::viewJSON($result);


    }

    public function getListCards(){
        $id = _MainModel::$params_url['id'];
        $result = _MainModel::table($this->bank)->get()->filter(array("id_user"=> $id))->send();
        _MainModel::viewJSON($result);
    }

    public function editUserCardStatus(){
        $card_id = _MainModel::$params_url['card_id'];
        $status = _MainModel::$params_url['status'];
        _MainModel::table($this->bank)->edit(array("status"=>$status), array("id"=> $card_id))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> $card_id))->send();
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
        //_MainModel::viewJSON($res);
        $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
        _MainModel::viewJSON($result);
    }
    public function deleteUser(){
        $id = _MainModel::$params_url['id'];
        _MainModel::table($this->table)->delete(array("id"=> $id))->send();
        $result = _MainModel::table($this->table)->get()->send();
        _MainModel::viewJSON($result);

    }

    public function editUser(){
        $id = _MainModel::$params_url['id'];
        $nickname = _MainModel::$params_url['nickname'];
        $status = _MainModel::$params_url['status'];
        _MainModel::table($this->table)->edit(array("nickname" => $nickname, "status" => $status), array("id" => $id))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> $id))->send();
        _MainModel::viewJSON($result);

    }
}

?>
