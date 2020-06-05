<?php
class Transactions extends _MainModel {
    private $table= "transactions";
    private $acc = "accounts";

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

    public function editTransactionStatus(){
        $this->requireParams(['id', 'status']);
        $status = self::$params_url['status'];
        $oldStatus = _MainModel::table($this->table)->get()->filter(array("id" => self::$params_url['id']))->send()[0]['status'];
        if(( $status == "unconfirmed" || $status == "blocked") && $oldStatus == "confirmed"){

            $query ="Update $this->acc set amount_of_money = amount_of_money - 
                    ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id))
                    where id = (select id_recipient_account from $this->table where id = :id);";

            $stmt = self::$db->prepare($query);
            try{
                $stmt->execute(array('id'=>self::$params_url['id']));
            }
            catch( PDOException $e ){
                $this->viewJSON(array('code'=>"-1",'error' =>$e->getMessage()));//не удалось снять
                die();
            }


            $query ="Update $this->acc set amount_of_money = amount_of_money + 
                    ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id))
                    where id = (select id_sender_account from $this->table where id = :id);";

            $stmt = self::$db->prepare($query);
            try{
                $stmt->execute(array('id'=>self::$params_url['id']));
            }
            catch( PDOException $e ){

                $this->viewJSON(array('code'=>"-3",'error' =>$e->getMessage()));//не удалось перечислить
                die();
            }

            _MainModel::table($this->table)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['id']))->send();
        }
        if($status == "confirmed" && $oldStatus == "unconfirmed"){
            $this->confirmTransaction();
        }

        //$result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        $this->viewJSON("1");
    }



    public function createTransaction(){

        $this->requireParams(['id_sender', 'id_recipient', 'money']);
        if(self::$params_url['id_sender'] == self::$params_url['id_recipient']){
            $this->viewJSON("-1");//если счет отправления и получения один и тот же
            die();
        }
        /*or id = (select id_currency from accounts where id = :sender)*/
        $query = "Select name, status From currencies where id = (SELECT id_currency FROM accounts where id = :sender) or
                    id = (SELECT id_currency FROM accounts where id = :recipient)";
        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient']));
        }
        catch( PDOException $e ){
            $this->viewJSON(array('code'=>"-3",'error' =>$e->getMessage()));//не удалось получить статусы валют
            die();
        }
        /*if(!($result_query = $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient'])))){
            $this->viewJSON(array('error' => "failed to execute the query"));
        }*/

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row){
            if($row['status'] != "available"){
                $this->viewJSON(array('code'=>"-4",'error' => "currency $row[name] is not available"));//валюта не доступна
                die();
            }
        }

        $query = "Select currency_rate from currency_rates where id_currency_1 = (select id_currency from accounts where id = :sender) and 
                 id_currency_2 = (select id_currency from accounts where id = :recipient)";

        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient']));
        }
        catch( PDOException $e ){
            $this->viewJSON(array('code'=>"-5",'error' =>$e->getMessage()));//не удалось получить курс валют
            die();
        }
        /*if(!($result_query = $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient'])))){
            $this->viewJSON(array('error' => "failed to execute the query"));
        }*/

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($rows == null){
            $this->viewJSON(array('code'=>"-6",'error' => "currency_rate not found"));//курс валют отсутствует
            die();
        }
        $currency_rate = $rows[0]['currency_rate'];
        $query = "Select nickname, users.status as user_status, accounts.status as account_status From accounts, users where accounts.id = :sender and users.id = (SELECT id_user From accounts where id = :sender) or accounts.id = :recipient and users.id = (SELECT id_user From accounts where id = :recipient)";
        "rate, statuses of currencies";

        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient']));
        }
        catch( PDOException $e ){
            $this->viewJSON(array('code'=>"-7",'error' =>$e->getMessage()));//не удалось получить статусы пользователей и счетов
            die();
        }
        /*if(!($result_query = $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient'])))){
            $this->viewJSON(array('error' => "failed to execute the query"));
        }*/

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row){
            if($row['user_status'] != "unblocked"){
                $this->viewJSON(array('code'=>"-8",'error' => "user $row[nickname] blocked"));//пользователь заблокирован
                die();
            }
            if($row['account_status'] != "unblocked"){
                $this->viewJSON(array('code'=>"-9",'error' => "$row[nickname] account  blocked"));//счет заблокирован
                die();
            }
        }

        $query= "INSERT INTO `transactions`(`id_sender_account`, `id_recipient_account`, `status`, `transaction_time`, `amount_of_money`, `currency_rate`) 
                 VALUES (:sender, :recipient, :status, :time, :money, :rate)";
        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient'], 'status' => "unconfirmed", 'time' => date("Y-m-d H:i:s"), 'money'=>self::$params_url['money'], 'rate' => $currency_rate));
        }
        catch( PDOException $e ){
            $this->viewJSON(array('code'=>"-10",'error' =>$e->getMessage()));//не удалось создать транзакцию
            die();
        }
        /*if(!($result_query = $stmt->execute(array('sender'=>self::$params_url['id_sender'], 'recipient'=>self::$params_url['id_recipient'], 'status' => "unconfirmed", 'time' => date("Y-m-d H:i:s"), 'money'=>self::$params_url['money'], 'rate' => $currency_rate)))){
            $this->viewJSON(array('error' => "failed to execute the query"));
        }*/
        $res = self::$db->lastInsertId();
        $this->viewJSON($res);
    }

    public function confirmTransaction(){
        $this->requireParams(['id']);

        $res = _MainModel::table($this->table)->get(array("status"))->filter(array("id"=>self::$params_url['id']))->send();
        $status = $res[0]['status'];
        if($status == "confirmed"){
            $this->viewJSON(array('code'=>"-1",'error' => "This transaction already confirmed"));
            die();
        }

        $query ="Update $this->acc set amount_of_money = amount_of_money - 
                    ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id))
                    where id = (select id_sender_account from $this->table where id = :id);";

        $stmt = self::$db->prepare($query);
        try{
        $stmt->execute(array('id'=>self::$params_url['id']));
        }
        catch( PDOException $e ){
            if($e->getCode() == 22003){
                _MainModel::table($this->table)->edit(array("status"=>"blocked"), array("id"=> self::$params_url['id']))->send();
                $this->viewJSON(array('code'=>"-3", 'error' =>"Сумма перевода превышает сумму на счете отправителя"));
            }
            else{
                $this->viewJSON(array('code'=>"-4",'error' =>$e->getMessage()));//возникла ошибка при переводе
            }
                die();
        }


        $query ="Update $this->acc set amount_of_money = amount_of_money + 
                    ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id))
                    where id = (select id_recipient_account from $this->table where id = :id);";

        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('id'=>self::$params_url['id']));
        }
        catch( PDOException $e ){
            $this->moneyBack();
            $this->viewJSON(array('code'=>"-5",'error' =>$e->getMessage()));//не удалось перевести деньги на счет получателя
            die();
        }

        _MainModel::table($this->table)->edit(array('status' => "confirmed"), array("id" => self::$params_url['id']))->send();

        /*$query = "Select * FROM $this->acc where id = (SELECT id_sender_account from $this->table where id = :id)";
        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('id'=>self::$params_url['id']));
        }
        catch( PDOException $e ){
            $this->viewJSON(array('error' =>$e->getMessage()));
            die();
        }*/
        $this->viewJSON("1");
    }

    private function moneyBack() {
        $query ="Update $this->acc set amount_of_money = amount_of_money + 
                    ((select amount_of_money from $this->table where id = :id)*(select currency_rate from $this->table where id = :id))
                    where id = (select id_sender_account from $this->table where id = :id);";

        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('id'=>self::$params_url['id']));
            //_MainModel::table($this->table)->edit(array("status"=>"unconfirmed"), array("id"=> self::$params_url['id']))->send();
        }
        catch( PDOException $e ){
            $this->viewJSON(array('code'=> '-6', 'error' =>$e->getMessage()));//не удалось вернуть деньги обратно
            die();
        }
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