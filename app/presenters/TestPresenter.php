<?php

class TestPresenter extends MainPresenter {

	public static $isSecurity = false;

	public function getListUsers(){ echo (new Users())->getListUsers(); }

	/* labels */
	public function ruLableTable(){ $this->renderLabel('rus', 'labelLayoutTable'); }

	//public function table(){ $this->render(["title" => "table", "type" => "widgets"]); }

	//public function hello(){ $this->render(["title" => "hello", "type" => "widgets"]); }

}

?>