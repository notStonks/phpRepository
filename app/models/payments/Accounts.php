<?php 

class Accounts extends _MainModel {
    private $table= "accounts";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListAccounts(){
        $params = array('filter', 'search', 'page', 'count');
        $request = _MainModel::table($this->table)->get();

        for($i = 0; $i < count($params); $i++) {
            if (self::is_var($params[$i])) {
                if ($params[$i] == 'filter')
                    $request = $request->filter(array('status' => self::$params_url[$params[$i]]));

                if ($params[$i] == 'search')
                    $request = $request->search(array('id_user' =>  self::$params_url[$params[$i]] ));
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

    public function getAccountInfo(){
        if(self::is_var('id')){
            $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "param 'id' do not found"));
        }
    }

    public function addAccount(){
        $params = array('id_user', 'id_currency');
        foreach ($params as $param) {
        if(!self::is_var($param)) {
            return $this->viewJSON(array('error' => "key $param does not found"));
            }
        }

        $res = _MainModel::table($this->table)->add(array("id_user" => self::$params_url['id_user'], "id_currency" => self::$params_url['id_currency'], "date_creation" => date("Y-m-d H:i:s"), "amount_of_money" => 0, "status" => "unblocked"))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
        return $this->viewJSON($result);
    }

    public function deleteAccount(){
        if(self::is_var('id')) {
            _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
            $result = _MainModel::table($this->table)->get()->send();
            return $this->viewJSON($result);
        }
        else{
            return $this->viewJSON(array('error' => "param 'id' do not found"));
        }
    }

    public function editAccountStatus(){
        $params = array('id', 'status');
        foreach ($params as $param) {
            if(!self::is_var($param)){
                return $this->viewJSON(array('error' => "param $param do not found"));
            }
        }

        _MainModel::table($this->table)->edit(array("status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> self::$params_url['id']))->send();
        return $this->viewJSON($result);
    }

}