<?php

class Users extends _MainModel {
    private $table= "users";
    private $bank = "bank_cards";
    private $acc = "accounts";

   /* public function __construct()
    {
        //parent::__construct();
    }*/

    public function getListUsers(){
        $params = array('page', 'count');
        //$pagArr = array( 'page' => 0, 'count' => 6);
        foreach ($params as $param) {
            if (!self::is_var($param))
                return $this->viewJSON(array('error' => "param $param do not found"));
        }

        $result = _MainModel::table($this->table)->get()->pagination(intval(self::$params_url['page']),intval(self::$params_url['count']))->send();
        return $this->viewJSON($result);
    }

    public function getListCards(){
        $params = array('id', 'page', 'count');

        foreach($params as $param) {
            if (!self::is_var($param)) {
                return $this->viewJSON(array('error' => "param $param do not found "));
            }
        }
        $result = _MainModel::table($this->bank)->get()->filter(array("id_user" => self::$params_url['id']))->pagination(intval(self::$params_url['page']),intval(self::$params_url['count']))->send();
        return $this->viewJSON($result);
    }

    public function getListAccounts(){
        $params = array('id', 'page', 'count');

        foreach($params as $param) {
            if (!self::is_var($param)) {
                return $this->viewJSON(array('error' => "param $param do not found "));
            }
        }
        $result = _MainModel::table($this->acc)->get()->filter(array("id_user" => self::$params_url['id']))->pagination(intval(self::$params_url['page']),intval(self::$params_url['count']))->send();
        return $this->viewJSON($result);
    }

    public function editUserCardStatus(){
        $params = array('card_id', 'status');
        foreach ($params as $param){
            if(!self::is_var($param)){
                return $this->viewJSON(array('error' => "key $param do not found"));
            }
        }
        _MainModel::table($this->bank)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['card_id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['card_id']))->send();
        return $this->viewJSON($result);
    }

    public function editUserCardName(){
        $params = array('card_id', 'user_name');
        foreach ($params as $param){
            if(!self::is_var($param)){
                return $this->viewJSON(array('error' => "key $param do not found"));
            }
        }
        _MainModel::table($this->bank)->edit(array("user_name"=>self::$params_url['user_name']), array("id"=> self::$params_url['card_id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['card_id']))->send();
        return $this->viewJSON($result);
    }

    public function getUserInfo(){
        if(self::is_var('id')){
            $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "key 'id' does not found"));
        }
    }

    public function addUser(){
        if(self::is_var('nickname')) {
            $res = _MainModel::table($this->table)->add(array("nickname" => self::$params_url['nickname'], "status" => "unblocked"))->send();
            $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "key 'nickname' does not found"));
        }
    }

    public function deleteUser(){
        if(self::is_var('id')) {
            _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
            $result = _MainModel::table($this->table)->get()->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "key 'id' does not found"));
        }
    }

    public function editUser(){
        $params = array('id', 'nickname', 'status');
        foreach ($params as $param) {
            if(!self::is_var(self::$params_url[$param])){
                return $this->viewJSON(array('error' => "param $param do not found"));
            }
        }
        _MainModel::table($this->table)->edit(array("nickname" => self::$params_url['nickname'], "status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        return $this->viewJSON($result);
    }
}

?>