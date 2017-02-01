<?php
namespace whitephp\view;

class Template extends \Smarty {
    
    public function load($template, $mode = null) {
        if($mode == null)   {
            return $this->createTemplate(VIEW_PATH . MODULE . DS . $template);
        }
        elseif($mode == 1)  {
            return $this->createTemplate(VIEW_PATH . $template);
        }
    }
    
    
}