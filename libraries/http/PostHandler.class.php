<?php
namespace whitephp\http;

class PostHandler
{
    function __construct(string $escapemethod = null, string $flags = null, string $encoding = null) {

    }
    
    function get($key) {
        return strip_tags($_POST[$key]);
    }
}

