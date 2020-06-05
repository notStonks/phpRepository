<?php

class LoginSys extends _MainModel
{
    private $table = "users_person_data";
    private $users = "users";

    public function Register(){
        $this->requireParams(['login', 'password','first_name', 'second_name','email']);
        //var_dump(self::is_var(self::$params_url['login']));
        /*if(!(self::is_var('login'))){
            $this->viewJSON("-4");
            die();
        }*/

        $request = _MainModel::table($this->table)->get()->filter(array('login' => self::$params_url['login'] ))->send();
        //var_dump($request);
        if($request != null){
            $this->viewJSON("-1");
            die();
        }
        $mas = _MainModel::table($this->table)->get(array('id'))->send();
        $prev = $mas[count($mas)-1]['id'];

        $passHash = password_hash(self::$params_url['password'], PASSWORD_BCRYPT);
        $countAfter = _MainModel::table($this->table)->add(array("login" => self::$params_url['login'], "password" => $passHash, "first_name"=>self::$params_url['first_name'],"second_name"=>self::$params_url['second_name'],"email"=>self::$params_url['email']))->send();

        if ($countAfter > $prev){
            _MainModel::table($this->users)->add(array("id" => $countAfter, "nickname" => self::$params_url['login'], "status" => "unblocked"))->send();
            $this->viewJSON($countAfter);
        }
        else $this->viewJSON("-3");



    }
    public function Login(){
        $this->requireParams(['login', 'password']);
        $password = self::$params_url['password'];
        $result = _MainModel::table($this->table)->get()->search(array('login' => self::$params_url['login']))->send();

            //if (self::is_var('login') && self::is_var('password')){

        if($result != null)
            if(password_verify($password, $result[0]['password']))
                $this->viewJSON($result[0]['id']);
            else $this->viewJSON("-1");//неверный пароль
        else $this->viewJSON("-3");//нет пользователя с таким логином
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
        /*$keys = array_keys(self::$params_url);
        $diff = array_diff($arr, $keys);
        if (!empty($diff)) {
            self::viewJSON(array('code'=>'-2','error' => implode(', ', $diff) . ' required'));
            die();
        }
    }*/
}
?>