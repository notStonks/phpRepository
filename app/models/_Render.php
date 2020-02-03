<?php
 
class _Render {


    function __construct () {
    }

    public static function viewJSON($json = null) {

        if(!is_null($json)){

            if(is_array($json)){
                $result = ["result" => self::siezeJsonToArray($json)];
                //array_push($result, array('result' => $this->siezeJsonToArray($json)));

            }else{

                $result = ["result" => $json];

            }

            if(!self::isMobile()){
                
                header('Content-type:application/json;charset=utf-8');
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }
            else {

                if(!empty($_GET['callback'])){

                    header('Content-Type: application/javascript');
                    echo $_GET['callback'] . ' (' . json_encode($result, JSON_UNESCAPED_UNICODE) . ');';

                }
                else{
                    echo "Error! Not callback !";
                }
            }
        }
        else{
            echo ("Empty data for view json");
        }

    }

    function view ($path, $data = []) {

        if (is_array($data))
            extract($data);

        require(ROOT . '/frontend/layouts/' . $path . '.php');

    }



    /**
     * @param  [type array $arr]
     * @return [type array]
     */
    private static function siezeJsonToArray($arr){

        foreach ($arr as $key => $value) {
            if(is_array($value)){
                foreach ($value as $k => $v) {

                    if(is_string($v)){

                        if ( is_object(json_decode($v)) ) { 

                            $arr[$key][$k] = json_decode($v, true);
            
                        }
                    }
                }
            } 
        }

        return $arr;
    }

    private static function isMobile() { 
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }



}

?>