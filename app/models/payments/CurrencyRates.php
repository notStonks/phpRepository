<?php


class CurrencyRates extends _MainModel
{
    private $table= "currency_rates";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListCurrencyRates(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('id_currency1' => "%" . self::$params_url['search'] . "%"));

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getCurrencyRateInfo(){
        if(self::is_var('id')){
            $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "key 'id' does not found"));
        }
    }

    public function addCurrencyRate(){
        $params = array('id_currency1','id_currency2', 'currency_rate');
        foreach ($params as $param){
        if(!self::is_var($param))
            return $this->viewJSON(array('error' => "key $param does not found"));
        }

        $res = _MainModel::table($this->table)->add(array("id_currency_1" => self::$params_url['id_currency1'], "id_currency_2" => self::$params_url['id_currency2'], "currency_rate" =>self::$params_url['currency_rate'], "date_time"=>date("Y-m-d H:i:s")))->send();
        return $this->viewJSON($res);
    }


    public function updRate(){
        $params = array('id', 'nickname', 'status');
        foreach ($params as $param)
        {
            if(!self::is_var($param)){
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