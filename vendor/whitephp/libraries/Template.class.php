<?php
namespace whitephp;

class Template extends \Smarty {
    
    public function load($template) {
        return $this->createTemplate(VIEW_PATH . MODULE . DS . $template);
    }
    
}