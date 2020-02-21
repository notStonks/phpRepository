<?php 

class Accounts extends _MainModel {
    private $table = "accounts";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

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
        $res = _MainModel::table($this->table)->add(array("id_user" => self::$params_url['id_user'], "id_currency" => self::$params_url['id_currency'], "date_creation" => date("Y-m-d H:i:s"), "amount_of_money" => 0, "status" => "unblocked"))->send();
        $this->viewJSON($res);
    }

    public function deleteAccount() {
        $this->requireParams(['id']);
        _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
        $result = _MainModel::table($this->table)->get()->send();
        $this->viewJSON($result);
    }

    public function editAccountStatus() {
        $this->requireParams(['id', 'status']);
        _MainModel::table($this->table)->edit(array("status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON($result);
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