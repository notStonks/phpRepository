<?php
 
class _AutoInclude {

    function autoload () {

    	$list_forbidden_dir = array('widgets', 'labels');

	    $list_dir = array_values(array_diff(scandir(ROOT . '/app/'), array('..', '.', '.DS_Store')));

        foreach ($list_dir as $key => $value) {

        	if(is_dir(ROOT . '/app/' . $value)){

        		if( !in_array($value, $list_forbidden_dir) ){
        		
	        		$list_dir_ch = array_values(array_diff(scandir(ROOT . '/app/' . $value), array('..', '.', '.DS_Store')));

	        		foreach ($list_dir_ch as $dir) {

	        			$path = ROOT . '/app/' . $value . '/' . $dir;

	        			if(is_file($path)){
	        				  require_once $path;
	        			}

	        			if(is_dir($path)){

	        				if( !in_array($value, $list_forbidden_dir) ){

			    				$list_dir_bootom = array_values(array_diff(scandir(ROOT . '/app/' . $value . '/' . $dir), array('..', '.', '.DS_Store')));

			    				foreach ($list_dir_bootom as $file) {

			    					$path = ROOT . '/app/' . $value . '/' . $dir . '/' . $file;

			    					if(is_file($path)){
			    						require_once $path;
			    					}
			    				}
			    			}

	        			}
	        		}
	        	}
        		
        	}

        }

    }


}

?>