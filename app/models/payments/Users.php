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
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('nickname' => "%" . self::$params_url['search'] . "%"));

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }


    public function getListCards(){
        $this->requireParams(['id']);
        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);
        $result = _MainModel::table($this->bank)->get()->filter(array("id_user" => self::$params_url['id']))->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getListAccounts(){
        $this->requireParams(['id']);
        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);
        $result = _MainModel::table($this->acc)->get()->filter(array("id_user" => self::$params_url['id']))->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function editUserCardStatus(){
        $this->requireParams(['card_id', 'status']);
        _MainModel::table($this->bank)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['card_id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['card_id']))->send();
        $this->viewJSON($result);
    }

    public function editUserCardName(){
        $this->requireParams(['card_id', 'user_name']);
        _MainModel::table($this->bank)->edit(array("user_name"=>self::$params_url['user_name']), array("id"=> self::$params_url['card_id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['card_id']))->send();
        $this->viewJSON($result);
    }

    public function getUserInfo(){
        if(self::is_var('id')){
            $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
            $this->viewJSON($result);
        }
        else{
            $this->viewJSON(array('error' => "key 'id' does not found"));
        }
    }

    public function addUser(){
        if(self::is_var('nickname')) {
            $res = _MainModel::table($this->table)->add(array("nickname" => self::$params_url['nickname'], "status" => "unblocked"))->send();
            return $this->viewJSON($res);
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

    private function requireParams($arr) {
        if (!is_array($arr))
            throw new InvalidArgumentException('array required');
        $keys = array_keys(self::$params_url);
        $diff = array_diff($arr, $keys);
        if (!empty($diff)) {
            self::viewJSON(array('error' => implode(', ', $diff) . ' required'));
            die();
        }
    }

    private function checkedInt($key, $default = 0, $arr = null) {
        if (is_null($arr))
            $arr = self::$params_url;
        if (isset($arr[$key])) {
            $val = $arr[$key];
            if (filter_var($val, FILTER_VALIDATE_INT) === false) {
                self::viewJSON(['error' => "invalid $key parameter type; must be int"]);
                die();
            }
            return intval($val);
        }
        return $default;
    }

}

?>