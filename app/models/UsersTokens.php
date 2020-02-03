<?php
 
class UsersTokens {


    public function isSecurity(){

        if( !empty($_COOKIE['security_key']) ) {
            
            $result = MainModel::table("users_security_key")->get(array('id_user'))->filter(array('security_key' => $_COOKIE['security_key']))->send();
            
            if( !empty($result[0]['id_user']) ){
               
                $this->security_id = $result[0]['id_user'];
                return true;

            }else{
                header('Location: https://' . $_SERVER['SERVER_NAME']);
            }

        }else{
            header('Location: https://' . $_SERVER['SERVER_NAME']);
        }
    }

}

?>