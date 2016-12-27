<?php

class IndexController {
    
    public function IndexAction() {
        $TemplateEngine = new whitephp\view\Template();
        $tpl = $TemplateEngine->load("index.tpl");
        $tpl->assign('world', 'World', true);
        $tpl->display();
        
    }
    
}