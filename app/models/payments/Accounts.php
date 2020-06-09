<?php 

class Accounts extends _MainModel {
    private $table = "accounts";
    private $users = "users";

    public function getListAccounts(){
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

    public function getAccountInfo(){
        $this->requireParams(['id']);
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON($result);
    }

    public function addAccount() {
        $this->requireParams(['id_user', 'id_currency']);
        $userStatus = _MainModel::table($this->users)->get()->filter(array("id" => self::$params_url['id_user']))->send()[0]['status'];
        if($userStatus == "blocked" ){
            $this->viewJSON("-1");//пользователь заблокирован
            die();
        }
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];
        $countAfter = _MainModel::table($this->table)->add(array("id_user" => self::$params_url['id_user'], "id_currency" => self::$params_url['id_currency'], "date_creation" => date("Y-m-d H:i:s"), "amount_of_money" => 0, "status" => "unblocked"))->send();
        if($countAfter > $prev)
        $this->viewJSON("1");
        else $this->viewJSON("-3");
    }

    public function deleteAccount() {
        $this->requireParams(['id']);
        $prev = count(_MainModel::table($this->table)->get(array('id'))->send());
        _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
        $countAfter = count(_MainModel::table($this->table)->get(array('id'))->send());
        if($countAfter < $prev)
            $this->viewJSON("1");
        else $this->viewJSON("-1");
    }

    public function editAccountStatus() {
        $this->requireParams(['id', 'status']);
        $userId = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send()[0]['id_user'];
        $userStatus = _MainModel::table($this->users)->get()->filter(array("id"=> $userId))->send()[0]['status'];

        if($userStatus == "blocked"){
            $this->viewJSON("-1");
            die();
        }
        _MainModel::table($this->table)->edit(array("status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
        $this->viewJSON("1");
    }
    public function balanceChange(){
        $this->requireParams(['id', 'money']);

        /*$curBal = _MainModel::table($this->table)->get(array("amount_of_money"))->filter(array("id"=> self::$params_url['id']))->send()[0]["amount_of_money"];
        $curBal += self::$params_url['money'];
        if($curBal <0)
        {
            $this->viewJSON("-1");//сумма снятия превышает баланс
            die();
        }*/
        $userId = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send()[0]['id_user'];
        $userStatus = _MainModel::table($this->users)->get()->filter(array("id"=> $userId))->send()[0]['status'];

        $accStatus = _MainModel::table($this->users)->get()->filter(array("id"=> self::$params_url['id']))->send()[0]['status'];

        if($userStatus == "blocked" || $accStatus  == "blocked"){
            $this->viewJSON("-1");
            die();
        }
        if(self::$params_url['money'] < 0){
            $this->viewJSON("-3");//значение суммы на счету не может быть отрицательным
            die();
        }
        _MainModel::table($this->table)->edit(array("amount_of_money" => self::$params_url['money']), array("id" => self::$params_url['id']))->send();
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