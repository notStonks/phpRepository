<?php
 
class _MainModel extends DB{

     //GET and POST params url
    public static $params_url = [];

    public static $query;

    private static $select = false;

    private static $delete = false;

    private static $table;

    //where
    private static $field = array();

    private static $filter = array();

    //sort
    private static $sort_field = '';

    private static $sort_type = '';

    //pagination
    private static $count_element = null;

    private static $number_page = null;

    //add
    private static $array_field_add = array();

    //edit
    private static $array_field_edit = array();

    private static $array_where = array();

    //delete
    private static $array_field_delete = array();

    //like
    private static $array_like = array();


    function __construct () {

        $this->connect();
        $this->setParams();
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

    public static function table($table){

        self::$table = $table;

        return new self;
    }

    public static function get($field = null){
        
        self::$select = true;

        if($field != null){

            if(is_array($field)){


                if( count($field) > 0){
                    
                    self::$field = $field;
                    self::$query = "SELECT ";
                    //цикл
                    foreach (self::$field as $k => $v) {
                        if( $k + 1 == count(self::$field) ) {
                            self::$query .= $v;
                        }else{
                            self::$query .= $v . ", ";
                        }
                    }
                    self::$query .= " FROM " . self::$table;
                }
                else{
                    self::$query = "SELECT * FROM " . self::$table;
                }
            }
            else{
                echo "ERROR! invalid parameter type in function Model get()";
                die();
            }
        }else{
            self::$query = "SELECT * FROM " . self::$table;
        }

        return new self;
    }

    public function sort($field, $type){

        if(!empty($field) && !empty($type)){

            self::$sort_field = $field;
            self::$sort_type = $type;

            if($type == "desc"){

                self::$query .= " ORDER BY $field DESC";

            }else if($type == 'asc'){

                self::$query .= " ORDER BY $field ASC";

            }else{
                
                echo "ERROR! ->sort() type is not found $type";
                die();

            }
        }

        return new self;
    }

    public static function filter($array = null){

        if(is_array($array)){
            if( count($array) > 0 ){
                self::$filter = $array;

                self::$query .= " WHERE ";

                $count = 0;
                foreach (self::$filter as $k => $v) {
                    if( $count + 1 == count(self::$filter) ) {
                         self::$query .= $k . "= :filter_".$k." ";
                    }else{
                         self::$query .= $k . "= :filter_".$k ." AND ";
                    }
                    $count += 1;
                }
            }
            else{
                echo "ERROR! ->filter() params count array " . count($array);
                die();
            }  
        }
        else{
            echo "ERROR! ->filter() invalid parameter ";
            die();
        }

        return new self;
    }

    public static function search($array){

        if( count($array) > 0 ){

            self::$array_like = $array;

            if( count(self::$filter) == 0){
                self::$query .= " WHERE ";
            }else{
                self::$query .= " AND ";
            }
            
            $count = 0;
            foreach (self::$array_like as $k => $v) {

                if( $count + 1 == count(self::$array_like) ) {
                     self::$query .= $k . " LIKE :field_like_". $k . " ";
                }else{
                     self::$query .= $k . " LIKE :field_like_".$k ."  AND ";
                }
                $count += 1;
            }
        }
        else{

            echo "ERROR! ->search() params count array " . count($filter);
            die();

        }

        return new self;
    }

    public static function pagination($number_page = null, $count_element = null){
        
        if( !is_null($number_page) && !is_null($count_element) && is_int($number_page) && is_int($count_element) ){

            self::$number_page = $number_page;
            self::$count_element = $count_element;
            self::$query .= " LIMIT :number_page, :count_element ";

        }
        else{
            echo "ERROR! ->pagination() invalid arguments!";
            die();
        }

        return new self;
    }

    public static function edit($array_field = null, $array_where = null){

        if( !is_null($array_field) && !is_null($array_where) && is_array($array_field) && is_array($array_where) ){

            if(count($array_field) > 0 && count($array_where) > 0){

                self::$array_where = $array_where;
                self::$array_field_edit = $array_field;
                self::$query = "UPDATE " . self::$table . " SET ";

                $count = 0;

                foreach (self::$array_field_edit as $k => $v) {

                    if( $count + 1 == count(self::$array_field_edit) ) {
                         self::$query .= $k . " = :field_edit_" . $k . " ";
                    }
                    else{
                         self::$query .= $k . " = :field_edit_". $k . ", ";
                    }

                    $count += 1;
                }

                self::$query .= " WHERE ";

                $count = 0;

                foreach (self::$array_where as $k => $v) {

                    if( $count + 1 == count(self::$array_where) ) {
                        self::$query .= $k . " = :field_where_". $k . " ";
                    }
                    else{
                        self::$query .= $k . " = :field_where_". $k . " AND ";
                    }

                    $count += 1;
                }
            }
        }else{
            echo "ERROR! ->edit() invalid arguments!";
            die();
        }

        return new self;
    }

    public static function add($array = null){

        if(!is_null($array) && is_array($array) ){

            if(count($array) > 0){

                self::$array_field_add = $array;
                self::$query = "INSERT INTO " . self::$table . " ( ";

                $count = 0;

                foreach (self::$array_field_add as $k => $v) {

                    if( $count + 1 == count(self::$array_field_add) ) {
                         self::$query .= "`" . $k . "`";
                    }
                    else{
                         self::$query .= "`".$k."`, ";
                    }

                    $count += 1;
                }

                self::$query .= " ) VALUES (";

                $count = 0;

                foreach (self::$array_field_add  as $k => $v) {

                    if( $count + 1 == count(self::$array_field_add) ) {
                         self::$query .= " :field_add_". $k ." ";
                    }
                    else{
                         self::$query .= " :field_add_". $k ." , ";
                    }

                    $count += 1;
                }

                self::$query .= " ) ";
            }
        }else{
            echo "ERROR! invalid arguments ->add()";
            die();
        }

        return new self;
    }

    public static function delete($array = null){

        if(!is_null($array) && is_array($array) && count($array) > 0){

            if( count($array) > 0 ){

                self::$delete = true;
                self::$query = "DELETE FROM " . self::$table . " WHERE ";
                self::$array_field_delete = $array;
                $count = 0;

                foreach (self::$array_field_delete  as $k => $v) {

                    if( $count + 1 == count(self::$array_field_delete) ) {
                         self::$query .= $k . "= :field_delete_". $k ." ";
                    }
                    else{
                         self::$query .= $k . "= :field_delete_". $k .", ";
                    }

                    $count += 1;
                }
            }
        }else{
            echo "ERROR! invalid arguments ->delete()";
            die();
        }

        return new self;
    }

    public static function send(){
        
        $sth = self::$db->prepare(self::$query);


        //select  filter
        if( count(self::$filter) > 0 ){
            foreach (self::$filter as $k => $v) {
                $sth->bindValue(":filter_".$k, $v, PDO::PARAM_STR);
            }
        }        

        //search
        if( count(self::$array_like) > 0 ){

            foreach (self::$array_like as $k => $v) {
                $sth->bindValue(":field_like_".$k, '%'.$v.'%', PDO::PARAM_STR);
            }
        }

        //edit
        if( count(self::$array_field_edit) > 0  && count(self::$array_where) > 0 ){

            
            foreach (self::$array_field_edit as $k => $v) {
                $sth->bindValue(":field_edit_" . $k, $v);
            }
    
            foreach (self::$array_where as $key => $val) {
                $sth->bindValue(":field_where_" . $key, $val);
            }

        }

        //add
        if( count(self::$array_field_add) > 0 ){

            foreach (self::$array_field_add as $key_add => $val_add) {
                $sth->bindValue(":field_add_".$key_add, $val_add, PDO::PARAM_STR);
            }

        }

        //delete
        if( count(self::$array_field_delete) > 0 ){

            foreach (self::$array_field_delete as $k => $v) {
                $sth->bindValue(":field_delete_" .$k, $v);
            }

        }

        //sort
        if( !empty(self::$sort_field) ){

            $sth->bindValue(":sort_field", "id", PDO::PARAM_STR);
            //$sth->bindParam($count, self::$sort_field);
        }

        //pagination
        if( isset(self::$number_page) && isset(self::$count_element) ){

            $sth->bindValue(":number_page", self::$number_page, PDO::PARAM_INT);
            $sth->bindValue(":count_element", self::$count_element, PDO::PARAM_INT);
            
        }

        try{
            $sth->execute();

            //select
            if(self::$select == true){

                self::clearProperty();
                return $sth->fetchAll(\PDO::FETCH_ASSOC);

            }
            
            //add or edit
            if( count(self::$array_field_add) || count(self::$array_field_edit) ){
                self::clearProperty();
                return self::$db->lastInsertId();
            }else if( self::$delete == false){
                $sth->fetchAll(\PDO::FETCH_ASSOC);
                self::clearProperty();
                die();
            }
            self::clearProperty();
        }
        catch( PDOException $e ){

            echo ("Error execute query " . $e->getMessage());
            $sth->debugDumpParams();
            self::clearProperty();
            die();

        }
    
    }

    public function getQuery(){
        echo self::$query;
        echo "<br><br>";
        

    }

    private function setParams(){

        $allowed_char = " \t\n\r\0\x0B'";

        if(count($_POST)){

            foreach ($_POST as $k => $v) {
                if(!empty($v)){
                    self::$params_url[$k] = trim(filter_input(INPUT_POST, $k), $allowed_char);
                }
            }

        }

        if(count($_GET)){

            foreach ($_GET as $k => $v) {

                if(!empty($v)){
                    self::$params_url[$k] = trim(filter_input(INPUT_GET, $k), $allowed_char);
                }
                
            }

        }

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

    private static function clearProperty(){

        self::$select = false;
        //where
        self::$field = array();

        self::$filter = array();

        //sort
        self::$sort_field = '';

        self::$sort_type = '';

        //pagination
        self::$count_element = null;

        self::$number_page = null;

        //add
        self::$array_field_add = array();

        //edit
        self::$array_field_edit = array();

        self::$array_where = array();

        //delete
        self::$array_field_delete = array();

        //like
        self::$array_like = array();

    }

    private static function isMobile() { 
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }



}

?>