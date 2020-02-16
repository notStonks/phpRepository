<?php
 //require_once "_MainModel.php";
class Users extends _MainModel {
    private $table= "users";
    private $bank = "bank_cards";

   /* public function __construct()
    {
        //parent::__construct();
    }*/

    public function getListUsers(){
        $result = _MainModel::table($this->table)->get()->send();
        $this->viewJSON($result);
    }

    public function getListCards(){
       if (array_key_exists('id', _MainModel::$params_url) ){
             $id = self::$params_url['id'];
            if(is_numeric($id) == false) {
                _MainModel::viewJSON(array('error' => array("text" => "invalid type of arg (must be int)", "code" => 4)));
                return;
            }
       }
       else {
           _MainModel::viewJSON(array('error' => array("text" => "key 'id' does not found", "code" => 2)));
           return;
       }
            $result = _MainModel::table($this->bank)->get()->filter(array("id_user" => $id))->send();
            $this->viewJSON($result);
    }

    public function editUserCardStatus(){
        $params = array('card_id', 'status');
        foreach ($params as $param){
            if(array_key_exists($param,self::$params_url)){
                if(is_numeric(self::$params_url['card_id']) == false) {
                    $this->viewJSON(array('error' => array("text" => "invalid type of arg (card_id must be int)", "code" => 4)));
                    return;
                }
            }
            else{
                $this->viewJSON(array('error' => array("text" => "key $param do not found", "code" => 2)));
                return;
            }
        }
        $card_id = self::$params_url['card_id'];
        $status = self::$params_url['status'];
        if(is_numeric($card_id) == false) {
            $this->viewJSON(array('error' => array("text" => "invalid type of arg (card_id must be int)", "code" => 4)));
            return;
        }

        _MainModel::table($this->bank)->edit(array("status"=>$status), array("id"=> $card_id))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> $card_id))->send();
        $this->viewJSON($result);
    }

    public function getUserInfo(){
        if(array_key_exists('id',self::$params_url)){
            $id = self::$params_url['id'];
        if(is_numeric($id) == false) {
            $this->viewJSON(array('error' => array("text" => "invalid type of arg (id must be int)", "code" => 4)));
            return;
            }
        }
        else{
            $this->viewJSON(array('error' => array("text" => "key 'id' does not found", "code" => 2)));
            return;
        }
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> $id))->send();
        $this->viewJSON($result);
    }
    public function addUser(){

        if(array_key_exists('nickname',self::$params_url))
            $nickname = self::$params_url['nickname'];
        else{
            $this->viewJSON(array('error' => array("text" => "key 'nickname' does not found", "code" => 2)));
            return;
        }
        $status = "unblocked";
        $res = _MainModel::table($this->table)->add(array("nickname" => $nickname, "status" => $status))->send();

        $result = _MainModel::table($this->table)->get()->filter(array("id"=>$res))->send();
        $this->viewJSON($result);
    }
    public function deleteUser(){
        if(array_key_exists('id',self::$params_url)) {
            $id = self::$params_url['id'];
            if(is_numeric($id) == false) {
                $this->viewJSON(array('error' => array("text" => "invalid type of arg (id must be int)", "code" => 4)));
                return;
            }
        }
        else{
            $this->viewJSON(array('error' => array("text" => "key 'id' does not found", "code" => 2)));
            return;
        }
        _MainModel::table($this->table)->delete(array("id"=> $id))->send();
        $result = _MainModel::table($this->table)->get()->send();
        $this->viewJSON($result);
    }

    public function editUser(){
        $params = array('id', 'nickname', 'status');
        foreach ($params as $param)
        {
            if(!array_key_exists($param,self::$params_url)){
                self::viewJSON(array('error' => array("text" => "key $param do not found", "code" => 2)));
                return;
            }
        }
        $id = self::$params_url['id'];
        if(is_numeric($id) == false) {
            _MainModel::viewJSON(array('error' => array("text" => "invalid type of arg (id must be int)", "code" => 4)));
            return;
        }
        $nickname = self::$params_url['nickname'];
        $status = self::$params_url['status'];
        _MainModel::table($this->table)->edit(array("nickname" => $nickname, "status" => $status), array("id" => $id))->send();
        $result = _MainModel::table($this->table)->get()->filter(array("id"=> $id))->send();
        $this->viewJSON($result);
    }
}

?>