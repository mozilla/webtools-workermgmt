<?php

/**
 * Class Debug - defines debugging convenience methods
 * @author Alexander Podgorny
 */
 
 
 class Debug {
     
     public static function show() {
		print '<pre style="border: 1px dashed #CCC; padding: 10px;">';
		foreach (func_get_args() as $mValue) {
		    if (!$mValue) {
 	        if (is_array($mValue)) {
 	            $mValue = 'Empty array';
	            } else if ($mValue === null) {
	                $mValue = 'null';
                } else if ($mValue === false) {
                 $mValue = 'false';
                }
	        }
		    if (is_object($mValue)) { $mValue = method_exists($mValue, '__toString') ? (string)$mValue : $mValue; }
		    print (htmlentities(print_r($mValue, 1)));
		    print "<br>";
	    }
		print '</pre>';
	}
	
    public static function showAndDie() {
	    print '<pre style="border: 1px dashed #CCC; padding: 10px;">';
		foreach (func_get_args() as $mValue) {
		    if (!$mValue) {
    	        if (is_array($mValue)) {
    	            $mValue = 'Empty array';
	            } else if ($mValue === null) {
	                $mValue = 'null';
                } else if ($mValue === false) {
                    $mValue = 'false';
                }
	        }
		    if (is_object($mValue)) { $mValue = method_exists($mValue, '__toString') ? (string)$mValue : $mValue; }
		    print (htmlentities(print_r($mValue, 1)));
		    print "<br>";
	    }
		print '</pre>';
		exit();
	}     
 }