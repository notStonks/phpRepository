<?php

class Transactions extends _MainModel {
    private $table= "transactions";
    private $acc = "accounts";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListTransactions(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('id_sender_account' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort')){
            $this->requireParams(['sort_field']);
            $request->sort(self::$params_url['sort_field'], self::$params_url['sort']);
        }

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

    public function getTransactionInfo(){
        $this->requireParams(['id']);
        $result = _MainModel::table($this->table)->get()->filter(array("id" => self::$params_url['id']))->send();
        $this->viewJSON($result);
    }

    public function createTransaction(){

        $this->requireParams(['id_sender', 'id_recipient', 'money']);
        $query= "INSERT INTO `transactions`(`id_sender_account`, `id_recipient_account`, `status`, `transaction_time`, `amount_of_money`, `currency_rate`) 
                 VALUES (:sender, :recipient, :status, :time, :money, 
                 (Select currency_rate from currency_rates where id_currency_1 = (select id_currency from accounts where id = :sender) and 
                 id_currency_2 = (select id_currency from accounts where id = :recipient)))";
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => "failed to prepare the query"));
        }
        if(!($result_query = $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient'], 'status' => "unconfirmed", 'time' => date("Y-m-d H:i:s"), 'money'=>self::$params_url['money']) ))){
            $this->viewJSON(array('error' => "failed to execute the query"));
        }
        $res = self::$db->lastInsertId();
        $this->viewJSON($res);
    }

    public function confirmTransaction(){
        $this->requireParams(['id']);

        $res = _MainModel::table($this->table)->get(array("status"))->filter(array("id"=>self::$params_url['id']))->send();
        $status = $res[0]['status'];
        if($status == "confirmed"){
            $this->viewJSON(array('error' => "This transaction already confirmed"));
        }

        $query ="Update $this->acc set amount_of_money = amount_of_money + 
                    ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id))
                    where id = (select id_recipient_account from $this->table where id = :id);";
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => "failed to prepare the query"));
        }
        if(!($stmt->execute(array('id'=>self::$params_url['id'])))){
           $this->viewJSON(array('error' => "failed to execute the query"));
        }
        _MainModel::table($this->table)->edit(array('status' => "confirmed"), array("id" => self::$params_url['id']))->send();


        $query = "Select * FROM $this->acc where id = (SELECT id_recipient_account from $this->table where id = :id)";
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => "failed to prepare the query"));
        }
        if(!($stmt->execute(array('id'=>self::$params_url['id'])))){
            $this->viewJSON(array('error' => "failed to execute the query"));
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->viewJSON($rows);
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