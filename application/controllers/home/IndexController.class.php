<?php

class IndexController {
    
    public function IndexAction() {
        $TemplateEngine = new Template();
        $tpl = $TemplateEngine->load("index.tpl");
        $tpl->assign('world', 'World', true);
        $tpl->display();
    }
    
}