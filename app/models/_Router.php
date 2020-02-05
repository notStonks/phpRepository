<?php
 
class _Router {

    private $route = [];

    function __construct (){ $this->initialization(); }

    private function initialization(){

        $this->route = explode('/', URI);

        if(!empty($this->route[3])){
            $arr = explode("?", $this->route[3]);
            $this->route[3] = $arr[0];
        }

        if(count($this->route) == 1){
            if(!empty($this->route[1])){
                $this->route[1] = strtolower($this->route[1]);
            }
        }

        if( !empty($this->route[1]) ){

            if($this->route[1] == 'api'){

                $path_presenter = ROOT . '/app/presenters/' . ucfirst($this->route[2]) . 'Presenter.php';

                if ( file_exists($path_presenter) ){
                    
                    define("PRESENTER", ucfirst($this->route[2]) . 'Presenter');

                    $presenter = ucfirst($this->route[2]) . 'Presenter';
                    $presenter = new $presenter();

                    if( !empty($this->route[3]) ) {

                        if( method_exists($presenter, $this->route[3]) ){

                            call_user_func_array(array($presenter, $this->route[3]), array());

                        }else{

                            echo "ERROR! Not found method  " . $this->route[3] . " in subproject " . $this->route[1];

                        }
                    }else{
                        
                        echo "ERROR! Method empty  ";
                    }
                }else{
                     echo "ERROR! Not found presenter " . $this->route[2];
                }
            }

            //
            //
            
        }else{
            echo "Generation 2 Framework version 2.0";
        }
    }



}

?>