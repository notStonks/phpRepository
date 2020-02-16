<?php
class Currencies extends _MainModel {
    private $table= "currencies";
    //private $bank = "bank_cards";

    /* public function __construct()
     {
         //parent::__construct();
     }*/

    public function getListCurrencies(){
        $query = "Select * from $this->table ";
        $resArr = array();
        $params = array('filter', 'search');
        $columns = array('status', 'name');
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
                    $query .= "$columns[$i] like :$params[$i]";
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

    public function getCurrencyInfo(){
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
}
?>