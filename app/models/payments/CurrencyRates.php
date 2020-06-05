<?php

class CurrencyRates extends _MainModel
{
    private $table = "currency_rates";
    private $cur = "currencies";
    public $updRes = 0;

    public function getListCurrencyRates(){
        $request = _MainModel::table($this->table)->get();

        if(self::is_var('search'))
            $request->search(array('id_currency1' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort')){
            $this->requireParams(['sort_field']);
            $request->sort(self::$params_url['sort_field'], self::$params_url['sort']);
        }

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getCurrencyRateInfo(){
        $this->requireParams(['id']);
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON($result);
    }

    public function addCurrencyRate(){
        $this->requireParams(['id_currency1','id_currency2', 'currency_rate']);
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];
        $res = _MainModel::table($this->table)->get()->filter(array("id_currency_1" => self::$params_url['id_currency1'], "id_currency_2" => self::$params_url['id_currency2']))->send();
        if($res != null){
            $this->viewJSON("-1");
        }
        else{
            $countAfter = _MainModel::table($this->table)->add(array("id_currency_1" => self::$params_url['id_currency1'], "id_currency_2" => self::$params_url['id_currency2'], "currency_rate" =>self::$params_url['currency_rate'], "date_time"=>date("Y-m-d H:i:s")))->send();
            if($countAfter > $prev)
            $this->viewJSON("1");
            else $this->viewJSON("-3");
        }

    }


    public function updRate(){
        $this->requireParams(['id', 'currency_rate']);

        _MainModel::table($this->table)->edit(array("currency_rate" =>self::$params_url['currency_rate'], "date_time"=>date("Y-m-d H:i:s")), array('id'=>self::$params_url['id']))->send();
        $this->viewJSON("1");
    }

    public function updRateCB(){
        $idRub = 1;
        if ($json_daily = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js')) {
            $data = json_decode($json_daily);
            $request = _MainModel::table($this->cur)->get()->send();

            foreach ($data->Valute as $key => $item) {
                for($i = 0; $i < count($request); $i++)
                    if($item->NumCode == $request[$i]["numCode"]){
                    _MainModel::table($this->table)->edit(array("currency_rate"=> $item->Value/$item->Nominal, "date_time"=>date("Y-m-d H:i:s")), array("id_currency_1"=>$request[$i]["id"], "id_currency_2" => 1))->send();
                    _MainModel::table($this->table)->edit(array("currency_rate"=> (1/($item->Value/$item->Nominal)), "date_time"=>date("Y-m-d H:i:s")), array("id_currency_2"=>$request[$i]["id"], "id_currency_1" => 1))->send();
                    }
            }

            for($i = 0; $i < count($request); $i++){
                $curId = $request[$i]['id'];
                $curIdRate = _MainModel::table($this->table)->get(array("currency_rate"))->filter(array("id_currency_1" => $curId, "id_currency_2" => $idRub))->send()[0]['currency_rate'];
                for($j = 0;$j < count($request); $j++){
                    if($request[$j]['id'] != $curId){
                        $valRate = _MainModel::table($this->table)->get(array("currency_rate"))->filter(array("id_currency_1" => $request[$j]["id"], "id_currency_2" => $idRub))->send()[0]['currency_rate'];
                        $rate = $curIdRate/$valRate;
                        _MainModel::table($this->table)->edit(array("currency_rate"=> $rate, "date_time"=>date("Y-m-d H:i:s")), array("id_currency_1"=>$curId, "id_currency_2" => $request[$j]["id"]))->send();
                    }
                }
            }
            $this->updRes = 1;
            $this->viewJSON("1");
        }
        else {
            $this->updRes = -1;
            $this->viewJSON("-1");}
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