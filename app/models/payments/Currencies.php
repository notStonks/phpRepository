<?php
class Currencies extends _MainModel {
    private $table= "currencies";
    //private $bank = "bank_cards";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListCurrencies(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('name' => "%" . self::$params_url['search'] . "%"));

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getCurrencyInfo(){
        if(self::is_var('id')){
            $result = _MainModel::table($this->table)->get()->filter(array("id" => self::$params_url['id']))->send();
            return $this->viewJSON($result);
        }
        else {
            return $this->viewJSON(array('error' => "param 'id' do not found"));
        }
    }

    public function addCurrency(){
        if(self::is_var('name')) {
            $res = _MainModel::table($this->table)->add(array("name" => self::$params_url['name'], "status" => "available"))->send();
            return $this->viewJSON($res);
        }
        else{
            return $this->viewJSON(array('error' => "param 'nickname' do not found"));
        }
    }

    public function editCurrencyStatus(){
        $params = array('id', 'status');

        foreach ($params as $param){
            if(!self::is_var($param)){
                return $this->viewJSON(array('error' => "param $param do not found"));
            }
        }

        _MainModel::table($this->table)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['id']))->send();
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