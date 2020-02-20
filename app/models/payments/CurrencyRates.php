<?php


class CurrencyRates extends _MainModel
{
    private $table= "currency_rates";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListCurrencyRates(){
        $params = array('page', 'count');
        foreach ($params as $param) {
            if (!self::is_var($param)) {
                return $this->viewJSON(array('error' => "param $param do not found"));
            }
        }

        $result = _MainModel::table($this->table)->get()->pagination(intval(self::$params_url['page']),intval(self::$params_url['count']))->send();
        return $this->viewJSON($result);
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
            return $this->viewJSON(array('error' => "key 'nickname' does not found"));
        }

        $res = _MainModel::table($this->table)->add(array("id_currency1" => self::$params_url['id_currency1'], "id_currency2" => self::$params_url['id_currency2'], "currency_rate" =>self::$params_url['currency_rate'], "date_time"=>date("Y-m-d H:i:s")))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
        return $this->viewJSON($result);
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

}