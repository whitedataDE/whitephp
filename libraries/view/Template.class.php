<?php
namespace whitephp\view;

class Template extends \Smarty {
    
    public function load($template, $mode = null) {
        if($mode == null)   {
            $tpl = $this->createTemplate(VIEW_PATH . MODULE . DS . $template);
        }
        elseif($mode == 1)  {
            $tpl = $this->createTemplate(VIEW_PATH . $template);
        }

        $public_path_user = \whitephp\helpers\directory_find_parent("public");
        $tpl->assign('PUBLIC', USER_PUBLIC_PATH);
        return $tpl;        
        
    }
    
    
}