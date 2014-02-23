<?php

class streamer_template extends Smarty {

    function __construct()
    {
        parent::__construct();

        $this->setTemplateDir(phpSM_DIR . 'layout/templates/');
        $this->setCompileDir(phpSM_DIR . 'var/templates_c/');
        $this->setConfigDir(phpSM_DIR . 'var/configs/');
        $this->setCacheDir(phpSM_DIR . 'var/cache/');

        // Caching disabled for development
        // $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $this->assign('app_name', 'Stream Manager');
    }
}
