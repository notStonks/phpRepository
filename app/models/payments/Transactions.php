<?php
class Transactions extends _MainModel {
    private $table= "transactions";
    private $acc = "accounts";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListTransactions(){
        $query = "Select * from $this->table ";
        $resArr = array();
        $params = array('filter', 'search');
        $columns = array('status', 'id_sender_account','id_recipient_account', 'transaction_time', 'amount_of_money');
        $k=0;
        for($i=0;$i<count($params);$i++){
            if(array_key_exists($params[$i], self::$params_url)){
                if($k==0) {
                    $query .= "Where ";
                }
                if($k!=0){
                    $query .= "AND ";
                }
                if($params[$i] == 'search'){
                    $query .= "($columns[$i] like :$params[$i] OR ".$columns[$i+1]." like :$params[$i] OR ".$columns[$i+2]." like :$params[$i] OR ".$columns[$i+3]." like :$params[$i]) ";
                    $resArr[$params[$i]] = "%" .self::$params_url[$params[$i]]. "%" ;
                }
                else{
                    $query .= "$columns[$i] = :$params[$i] ";
                    $resArr[$params[$i]] = self::$params_url[$params[$i]];
                }
                $k++;
            }
        }
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => array("text" => "failed to prepare the query", "code" => 6)));
        }
        if(!($result_query = $stmt->execute($resArr))){
            $this->viewJSON(array('error' => array("text" => "failed to execute the query", "code" => 7)));}
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->viewJSON($rows);
    }

    public function getTransaction(){
        if (array_key_exists('id', self::$params_url) ){
            $id = self::$params_url['id'];
            if(is_numeric($id) == false) {
                $this->viewJSON(array('error' => array("text" => "invalid type of arg (must be int)", "code" => 4)));
                return;
            }
        }
        else {
            $this->viewJSON(array('error' => array("text" => "key 'id' does not found", "code" => 2)));
            return;
        }
        $result = _MainModel::table($this->table)->get()->filter(array("id" => $id))->send();
        $this->viewJSON($result);
    }

    public function createTransaction(){
        $params = array('id_sender', 'id_recipient', 'money');
        foreach ($params as $param)
        {
            if(array_key_exists($param,self::$params_url)){
                if(is_numeric(self::$params_url[$param]) == false) {
                    $this->viewJSON(array('error' => array("text" => "invalid type of arg (must be numeric)", "code" => 4)));
                    return;
                }
            }
            else{
                $this->viewJSON(array('error' => array("text" => "key $param do not found", "code" => 2)));
                return;
            }
        }
        $status = "unchecked";
        /*$query = "Select currency_rate from currency_rates where id_currency_1 = (select id_currency from accounts where id = :sender) and id_currency_2 = (select id_currency from accounts where id = :recipient)";
        $stmt = self::$db->prepare($query);
        $result_query = $stmt->execute(array('sender'=>self::$params_url[$params[0]], 'recipient'=>self::$params_url[$params[1]]));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $currencyRate = $rows[0]['currency_rate'];*/

        $query= "INSERT INTO `transactions`(`id_sender_account`, `id_recipient_account`, `status`, `transaction_time`, `amount_of_money`, `currency_rate`) VALUES (:sender, :recipient, :status, :time, :money, (Select currency_rate from currency_rates where id_currency_1 = (select id_currency from accounts where id = :sender) and id_currency_2 = (select id_currency from accounts where id = :recipient)))";
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => array("text" => "failed to prepare the query", "code" => 6)));
        }
        if(!($result_query = $stmt->execute(array('sender'=>self::$params_url[$params[0]], 'recipient'=>self::$params_url[$params[1]], 'status' => $status, 'time' => date("Y-m-d H:i:s"), 'money'=>self::$params_url[$params[2]]) ))){
            $this->viewJSON(array('error' => array("text" => "failed to execute the query", "code" => 7)));
        }
        $res = self::$db->lastInsertId();
        $this->viewJSON($res);
    }

    public function confirmTransaction(){
        if(array_key_exists('id',self::$params_url)){
            if(is_numeric(self::$params_url['id']) == false) {
                $this->viewJSON(array('error' => array("text" => "invalid type of arg (must be numeric)", "code" => 4)));
                return;
            }
        }
        else{
            $this->viewJSON(array('error' => array("text" => "key id do not found", "code" => 2)));
            return;
        }
        $res = _MainModel::table($this->table)->get(array("status"))->filter(array("id"=>self::$params_url['id']))->send();
        $status = $res[0]['status'];
        if($status == "checked"){
            $this->viewJSON(array('error' => array("text" => "This transaction already confirmed", "code" => 5)));
            return;
        }

        $query ="Update $this->acc set amount_of_money = amount_of_money + ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id)) where id = (select id_recipient_account from $this->table where id = :id);";
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => array("text" => "failed to prepare the query", "code" => 6)));
        }
       if(!($stmt->execute(array('id'=>self::$params_url['id'])))){
           $this->viewJSON(array('error' => array("text" => "failed to execute the query", "code" => 7)));
       }
        _MainModel::table($this->table)->edit(array('status' => "checked"), array("id" => self::$params_url['id']))->send();


        $query = "Select * FROM $this->acc where id = (SELECT id_recipient_account from $this->table where id = :id)";
        if(!($stmt = self::$db->prepare($query))){
            $this->viewJSON(array('error' => array("text" => "failed to prepare the query", "code" => 6)));
        }
        if(!($stmt->execute(array('id'=>self::$params_url['id'])))){
            $this->viewJSON(array('error' => array("text" => "failed to execute the query", "code" => 7)));
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->viewJSON($rows);
    }
}
?>