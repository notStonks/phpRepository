<?php

class TestPresenter extends MainPresenter {

	public static $isSecurity = false;

	public function getListUsers(){ echo (new Users())->getListUsers(); }

	/* labels */
	public function ruLableTable(){ $this->renderLabel('rus', 'labelLayoutTable'); }


}

?>