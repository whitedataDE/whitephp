<?php
namespace whitephp;

class PostArray
{
    function __construct(string $escapemethod = null, string $flags = null, string $encoding = ini_get("default_charset")) 
    {
        
        $postarray = [];
        
        switch ($escapemethod)
        {
               
            case 'htmlspecialchars':
                if($flags == null) $flags = "ENT_COMPAT | ENT_HTML401";
             
                foreach (array_keys($_POST) as $key)
                {
                    $postarray[$key] = strip_tags($_POST[$key]);
                }
                break;
                
            default:
                
                foreach (array_keys($_POST) as $key)
                {
                    $postarray[$key] = strip_tags(call_user_func($escapemethod, $_POST[$key]));
                }
        }
        
        return $postarray;
    }
}

