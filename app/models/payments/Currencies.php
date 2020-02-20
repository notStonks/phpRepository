<?php
class Currencies extends _MainModel {
    private $table= "currencies";
    //private $bank = "bank_cards";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListCurrencies(){
        $params = array('filter', 'search', 'page', 'count');
        $request = _MainModel::table($this->table)->get();

        for($i = 0; $i < count($params); $i++) {
            if (self::is_var($params[$i])) {
                if ($params[$i] == 'filter')
                    $request = $request->filter(array('status' => self::$params_url[$params[$i]]));

                if ($params[$i] == 'search')
                    $request = $request->search(array('name' => "%" . self::$params_url[$params[$i]] . "%"));
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
            $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
            return $this->viewJSON($result);
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
}
?>