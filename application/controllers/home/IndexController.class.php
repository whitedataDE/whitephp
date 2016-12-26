<?php

class IndexController {
    
    public function IndexAction() {
        $TemplateEngine = new WhiteTemplate();
        $tpl = $TemplateEngine->load("index.tpl");
        $tpl->assign('world', 'World', true);
        $tpl->display();
    }
    
}