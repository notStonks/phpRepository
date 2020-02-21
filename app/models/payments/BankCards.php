<?php

class BankCards extends _MainModel {
    private $table= "bank_cards";


    public function getListCards(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('id_user' => "%" . self::$params_url['search'] . "%"));

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getCardInfo(){
        if(self::is_var('id')){
            $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "param 'id' do not found"));
        }
    }

    public function addCard(){
        $params = array('id_user', 'card_number', 'holder_name', 'end_date', 'cvc', 'user_name');

        foreach ($params as $param) {
            if(!self::is_var($param)) {
                return $this->viewJSON(array('error' => "param $param do not found"));
            }
        }

        $res = _MainModel::table($this->table)->add(array("id_user" => self::$params_url['id_user'], 'card_number' => self::$params_url['card_number'],'holder_name' => self::$params_url['holder_name'], 'end_date' => self::$params_url['end_date'],'status' => "available", 'cvc' =>self::$params_url['cvc'], 'user_name' => self::$params_url['user_name']))->send();
        return $this->viewJSON($res);
    }

    public function deleteCard(){
        if(self::is_var('id')) {
            _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
            $result = _MainModel::table($this->table)->get()->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "param 'id' do not found"));
        }
    }

    public function editCardStatus(){
        $params = array('id', 'status');
        foreach ($params as $param){
            if(!self::is_var($param)){
                return $this->viewJSON(array('error' => "key $param do not found"));
            }
        }
        _MainModel::table($this->table)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['id']))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        return $this->viewJSON($result);
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