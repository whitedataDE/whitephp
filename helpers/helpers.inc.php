<?php

namespace whitephp\helpers;

/**
 * @param unknown $array
 * @param unknown $needle
 * @param unknown $parent
 * @return unknown|string|boolean
 */

function array_find_parent($array, $needle_key, $needle_value, $parent = null) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $pass = $parent;
            if (is_string($key)) {
                $pass = $key;
            }
            $found = array_find_parent($value, $needle_key, $needle_value, $pass);
            if ($found !== false) {
                return $found;
            }
        } else if ($key === $needle_key && $value === $needle_value) {
            return $parent;
        }
    }

    return false;
}


function directory_find_parent($needle_directory, $path = null) {
    
    if(is_dir ($path . $needle_directory)) {
        return $path . $needle_directory;
    }
    else {
        directory_find_parent($needle_directory, $path . '../');   
    }
    
    
}