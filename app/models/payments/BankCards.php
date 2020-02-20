<?php

class BankCards extends _MainModel {
    private $table= "bank_cards";


    public function getListCards(){
        $params = array('filter', 'search', 'page', 'count');
        $request = _MainModel::table($this->table)->get();

        for($i = 0; $i < count($params); $i++) {
            if (self::is_var($params[$i])) {
                if ($params[$i] == 'filter')
                    $request = $request->filter(array('status' => self::$params_url[$params[$i]]));

                if ($params[$i] == 'search')
                    $request = $request->search(array('id_user' =>  self::$params_url[$params[$i]] ));
            }
            else {
                if($i > 1){
                    return $this->viewJSON(array('error' => "param $params[$i] do not found"));
                }
            }
        }

        $result = $request->pagination(intval(self::$params_url['page']),intval(self::$params_url['count']))->send();
        return $this->viewJSON($result);
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
        $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
        return $this->viewJSON($result);
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
        _MainModel::table($this->bank)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['id']))->send();
        return $this->viewJSON($result);
    }
}

?>