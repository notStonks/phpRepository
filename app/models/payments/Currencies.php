<?php
class Currencies extends _MainModel {
    private $table = "currencies";
    private $rates = "currency_rates";

    public function getListCurrencies(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('name' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort')){
            $this->requireParams(['sort_field']);
            $request->sort(self::$params_url['sort_field'], self::$params_url['sort']);
        }

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getCurrencyInfo(){
        $this->requireParams(['id']);
        $result = _MainModel::table($this->table)->get()->filter(array("id" => self::$params_url['id']))->send();
        $this->viewJSON($result);

    }

    public function addCurrency(){
        $this->requireParams(['charCode', 'numCode', 'full_name']);
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];
        $res = _MainModel::table($this->table)->get(array("name"))->filter(array("name" => self::$params_url['charCode']))->send();
        if($res != null){
            $this->viewJSON("-1");
        }
        else{
            $countAfter = _MainModel::table($this->table)->add(array("name" => self::$params_url['charCode'], "numCode" => self::$params_url['numCode'], "full_name" => self::$params_url['full_name'], "status" => "available"))->send();
            $ids = _MainModel::table($this->table)->get(array("id"))->send();
            $numCurr = count($ids);

            _MainModel::table($this->rates)->add(array("id_currency_1" => $countAfter, "id_currency_2" => $countAfter, "currency_rate" => 1, "date_time"=>date("Y-m-d H:i:s")))->send();
            for($i = 0;$i < $numCurr-1; $i++){
                if($countAfter != $ids[$i]['id']){
                _MainModel::table($this->rates)->add(array("id_currency_1" => $countAfter, "id_currency_2" => $ids[$i]['id'], "currency_rate" => 0, "date_time"=>date("Y-m-d H:i:s")))->send();
                _MainModel::table($this->rates)->add(array("id_currency_1" => $ids[$i]['id'], "id_currency_2" => $countAfter, "currency_rate" => 0, "date_time"=>date("Y-m-d H:i:s")))->send();
                }
            }
            $rates = new CurrencyRates();
            $rates->updRateCB();
            if($rates->updRes == 1){
                if($countAfter > $prev)
                    $this->viewJSON("1");
                else $this->viewJSON("-4");}
            else $this->viewJSON("-3");

        }
    }

    /*public function addCurrency1(){


            $res = self::$params_url['id'];

            $ids = _MainModel::table($this->table)->get(array("id"))->send();
            $numCurr = count($ids);
            //var_dump();
            //_MainModel::table($this->rates)->add(array("id_currency_1" => $res, "id_currency_2" => $res, "currency_rate" => 1, "date_time"=>date("Y-m-d H:i:s")))->send();
            for($i = 0;$i < $numCurr; $i++){
                if($res != $ids[$i]['id']){
                _MainModel::table($this->rates)->add(array("id_currency_1" => $res, "id_currency_2" => $ids[$i]['id'], "currency_rate" => 0, "date_time"=>date("Y-m-d H:i:s")))->send();
                _MainModel::table($this->rates)->add(array("id_currency_1" => $ids[$i]['id'], "id_currency_2" => $res, "currency_rate" => 0, "date_time"=>date("Y-m-d H:i:s")))->send();
                }
            }
            //(new CurrencyRates())->updRateCB();
            if($res > 0)
                $this->viewJSON("1");
            //$this->viewJSON($res);
    }*/

    public function editCurrency(){
        $this->requireParams(['id']);
        $paramsArray = array();
       // $request = _MainModel::table($this->table);

        if (self::is_var('charCode'))
            $paramsArray['name'] = self::$params_url['charCode'];
        if (self::is_var('numCode'))
            $paramsArray['numCode'] = self::$params_url['numCode'];
        if (self::is_var('full_name'))
            $paramsArray['full_name'] = self::$params_url['full_name'];
        if (self::is_var('status'))
            $paramsArray['status'] = self::$params_url['status'];

        _MainModel::table($this->table)->edit($paramsArray, array("id"=> self::$params_url['id']))->send();
        //$result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON("1");
    }

    public function editCurrencyStatus(){
        $this->requireParams(['id', 'status']);

        _MainModel::table($this->table)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['id']))->send();
        //$result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
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