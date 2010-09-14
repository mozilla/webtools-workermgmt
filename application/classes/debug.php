<?php

/**
 * Class Debug - defines debugging convenience methods
 * @author Alexander Podgorny
 */
 
 
 class Debug {
     
     public static function show($m_value) {
         print '<pre>'.print_r($m_value, 1).'</pre>';
     }
     
 }