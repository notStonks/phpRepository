<?php

class BankCards extends _MainModel {
    private $table = "bank_cards";
    private $users = "users";

    public function getListCards(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('id_user' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort')){
            $this->requireParams(['sort_field']);
            $request->sort(self::$params_url['sort_field'], self::$params_url['sort']);
        }

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getCardInfo(){
        $this->requireParams(['id']);
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON($result);
    }

    public function addCard(){
        $this->requireParams(['id_user', 'card_number', 'holder_name', 'end_date', 'cvc', 'user_name']);
        $userStatus = _MainModel::table($this->users)->get()->filter(array("id" => self::$params_url['id_user']))->send()[0]['status'];
        if($userStatus == "blocked" ){
            $this->viewJSON("-1");//пользователь заблокирован
            die();
        }
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];
        $countAfter = _MainModel::table($this->table)->add(array("id_user" => self::$params_url['id_user'], 'card_number' => self::$params_url['card_number'],'holder_name' => self::$params_url['holder_name'], 'end_date' => self::$params_url['end_date'],'status' => "available", 'cvc' =>self::$params_url['cvc'], 'user_name' => self::$params_url['user_name']))->send();
        if($countAfter > $prev)
            $this->viewJSON("1");
        else $this->viewJSON("-3");
    }

    public function deleteCard(){
        $this->requireParams(['id']);
        $prev = count(_MainModel::table($this->table)->get(array('id'))->send());
        _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
        $countAfter = count(_MainModel::table($this->table)->get(array('id'))->send());
        if($countAfter < $prev)
            $this->viewJSON("1");
        else $this->viewJSON("-1");
    }

    public function editCardName(){
        $this->requireParams(['id', 'user_name']);
        $userId = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send()[0]['id_user'];
        $userStatus = _MainModel::table($this->users)->get()->filter(array("id"=> $userId))->send()[0]['status'];

        if($userStatus == "blocked"){
            $this->viewJSON(array('code'=>"-1",'error' =>"The user of this card is blocked"));
            die();
        }
        _MainModel::table($this->table)->edit(array("user_name"=>self::$params_url['user_name']), array("id"=> self::$params_url['id']))->send();

        $this->viewJSON("1");
    }

    public function editCardStatus(){
        $this->requireParams(['id', 'status']);
        $userId = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send()[0]['id_user'];
        $userStatus = _MainModel::table($this->users)->get()->filter(array("id"=> $userId))->send()[0]['status'];

        if($userStatus == "blocked"){
            $this->viewJSON(array('code'=>"-1",'error' =>"The user of this card is blocked"));
            die();
        }
        _MainModel::table($this->table)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['id']))->send();

        $this->viewJSON("1");
    }

    private function requireParams($arr) {
        if (!is_array($arr))
            throw new InvalidArgumentException('array required');

        $require = array();
        foreach ($arr as $val)
            if(!self::is_var($val))
                array_push($require, $val);


        if(!empty($require)){
            self::viewJSON(array('code'=>'-2','error' => implode(', ', $require) . ' required'));
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