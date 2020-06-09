<?php
class Users extends _MainModel {
    private $table= "users";
    private $person_data= "users_person_data";
    private $bank = "bank_cards";
    private $acc = "accounts";

    public function getListUsers(){
        $request = _MainModel::table($this->table)->get();

        if (self::is_var('filter'))
            $request->filter(array('status' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('nickname' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort'))
            $request->sort('nickname', self::$params_url['sort']);

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();

        $this->viewJSON($result);
    }

    public function getListPersonData(){
        $request = _MainModel::table($this->person_data)->get();

        if (self::is_var('filter'))
            $request->filter(array('second_name' => self::$params_url['filter']));
        if(self::is_var('search'))
            $request->search(array('login' => "%" . self::$params_url['search'] . "%"));
        if(self::is_var('sort'))
            $request->sort('login', self::$params_url['sort']);

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = $request->pagination($page,$count)->send();

        $this->viewJSON($result);
    }
   /* public function getListCards(){
        $this->requireParams(['id']);

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = _MainModel::table($this->bank)->get()->filter(array("id_user" => self::$params_url['id']))->pagination($page,$count)->send();
        $this->viewJSON($result);
    }
*/
    public function getListAccounts(){
        $this->requireParams(['id']);

        $page = $this->checkedInt('page', 1);
        $count = $this->checkedInt('count', 10);

        $result = _MainModel::table($this->acc)->get()->filter(array("id_user" => self::$params_url['id']))->pagination($page,$count)->send();
        $this->viewJSON($result);
    }

  /*  public function editUserCardStatus(){
        $this->requireParams(['card_id', 'status']);

        _MainModel::table($this->bank)->edit(array("status"=>self::$params_url['status']), array("id"=> self::$params_url['card_id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['card_id']))->send();
        $this->viewJSON($result);
    }
*/
  /*  public function editUserCardName(){
        $this->requireParams(['card_id', 'user_name']);

        _MainModel::table($this->bank)->edit(array("user_name"=>self::$params_url['user_name']), array("id"=> self::$params_url['card_id']))->send();
        $result = _MainModel::table($this->bank)->get()->filter(array("id"=> self::$params_url['card_id']))->send();
        $this->viewJSON($result);
    }
*/
    public function getUserInfo(){
        $this->requireParams(['id']);
        $query ="SELECT users.id, login,first_name, second_name,email,status FROM $this->table, $this->person_data 
                    WHERE users.id = :id and users_person_data.id = :id;";

        $stmt = self::$db->prepare($query);
        try{
            $stmt->execute(array('id'=>self::$params_url['id']));
        }
        catch( PDOException $e ){
            $this->viewJSON(array('code'=> '-1', 'error' => $e->getMessage()));
            die();
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->viewJSON($rows);
    }

    public function addUser(){
        $this->requireParams(['nickname']);
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];
        $countAfter = _MainModel::table($this->table)->add(array("nickname" => self::$params_url['nickname'], "status" => "unblocked"))->send();
        if($countAfter > $prev)
            $this->viewJSON("1");
        else $this->viewJSON("-1");
    }

    public function deleteUser(){
        $this->requireParams(['id']);
        $countBefore = count(_MainModel::table($this->table)->get(array('id'))->send());
        $countBefore1 = count(_MainModel::table($this->person_data)->get(array('id'))->send());
        _MainModel::table($this->table)->delete(array("id"=> self::$params_url['id']))->send();
        _MainModel::table($this->person_data)->delete(array("id"=> self::$params_url['id']))->send();
        $countAfter = count(_MainModel::table($this->table)->get(array('id'))->send());
        $countAfter1 = count(_MainModel::table($this->person_data)->get(array('id'))->send());
        if(($countAfter < $countBefore) && ($countAfter1 < $countBefore1))
            $this->viewJSON("1");
        else $this->viewJSON("-1");
    }
    private function AccountsStatusChange(){
        $this->requireParams(['id', 'status']);
        $status = self::$params_url['status'];
        $oldStatus = _MainModel::table($this->table)->get()->filter(array("id" => self::$params_url['id']))->send()[0]['status'];
        if(($status == "blocked" && $oldStatus == "unblocked") || ($status == "unblocked"  && $oldStatus == "blocked")){
            _MainModel::table($this->acc)->edit(array("status" => self::$params_url['status']), array("id_user" => self::$params_url['id']))->send();
        }
 }
    public function editUser(){
        $this->requireParams(['id', 'status','login','first_name', 'second_name','email']);

        $allLogins = _MainModel::table($this->person_data)->get(array("login"))->filter(array('login' => self::$params_url['login']))->send()[0]['login'];

        $userLogin = _MainModel::table($this->person_data)->get(array("login"))->filter(array('id' => self::$params_url['id'] ))->send()[0]['login'];

        if(($userLogin != self::$params_url['login'])&&($allLogins == self::$params_url['login'])){
            $this->viewJSON("-1");//данный логин занят
            die();
        }
        _MainModel::table($this->table)->edit(array("nickname" => self::$params_url['login'], "status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();

        _MainModel::table($this->person_data)->edit(array("login" => self::$params_url['login'], "first_name" => self::$params_url['first_name'], "second_name" => self::$params_url['second_name'], "email" => self::$params_url['email']), array("id" => self::$params_url['id']))->send();
        $this->AccountsStatusChange();
        $this->viewJSON("1");
    }
    public function editPassword(){
        $this->requireParams(['id','password']);

        $passHash = password_hash(self::$params_url['password'], PASSWORD_BCRYPT);
        _MainModel::table($this->person_data)->edit(array("password" => $passHash), array("id" => self::$params_url['id']))->send();

        $this->viewJSON("1");
    }

    public function editUserStatus(){
        $this->requireParams(['id', 'status']);
        _MainModel::table($this->table)->edit(array( "status" => self::$params_url['status']), array("id" => self::$params_url['id']))->send();
        $this->AccountsStatusChange();
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