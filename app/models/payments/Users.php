<?php
class Users extends _MainModel {
    private $table= "users";
    private $bank = "bank_cards";
    private $acc = "accounts";

    public function getListUsers(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('nickname' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort'))
            $request->sort('nickname', self::$params_url['sort']);

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();

        $this->viewJSON($result);
    }

    public function getListAccounts(){
        $this->requireParams(['id']);

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = _MainModel::table($this->acc)->get()->filter(array("id_user" => self::$params_url['id']))->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getUserInfo(){
        $this->requireParams(['id']);

        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON($result);
    }

    public function addUser(){
        $this->requireParams(['nickname']);
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];
        $countAfter = _MainModel::table($this->table)->add(array("nickname" => self::$params_url['nickname'], "status" => "unblocked"))->send();
        if($countAfter > $prev)
            $this->viewJSON("1");
        else $this->viewJSON("-1");
    }

    public function deleteUser(){
        $this->requireParams(['id']);
        $countBefore = count(_MainModel::table($this->table)->get(array('id'))->send());
        _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
        $countAfter = count(_MainModel::table($this->table)->get(array('id'))->send());
        if($countAfter < $countBefore)
            $this->viewJSON("1");
        else $this->viewJSON("-1");
    }
    private function AccountsStatusChange(){
        $status = self::$params_url['status'];
        $oldStatus = _MainModel::table($this->table)->get()->filter(array("id" => self::$params_url['id']))->send()[0]['status'];
        if(($status == "blocked" && $oldStatus == "unblocked") || ($status == "unblocked"  && $oldStatus == "blocked")){
            _MainModel::table($this->acc)->edit(array("status" => self::$params_url['status']), array("id_user" => self::$params_url['id']))->send();
        }
 }
    public function editUser(){
        $this->requireParams(['id', 'nickname', 'status']);
        $this->AccountsStatusChange();
        _MainModel::table($this->table)->edit(array("nickname" => self::$params_url['nickname'], "status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
        //$result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON("1");
    }
    public function editUserStatus(){
        $this->requireParams(['id', 'status']);
        $this->AccountsStatusChange();
        _MainModel::table($this->table)->edit(array( "status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
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