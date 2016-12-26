<?php
class whitephp {
    
    public static function run() {

        self::init();
        self::autoload();
        self::dispatch();
    
    }
    
    private static function init() {
        
        // Define path constants
        
        define("DS", DIRECTORY_SEPARATOR);
        
        define("ROOT", getcwd() . DS);
        define("APP_PATH", ROOT . 'application' . DS);
        define("WHITEPHP_PATH", ROOT . "vendor" . DS . "whitephp" . DS);
        define("PUBLIC_PATH", ROOT . "public" . DS);
        
        define("CONFIG_PATH", APP_PATH . "config" . DS);
        define("CONTROLLER_PATH", APP_PATH . "controllers" . DS);
        define("MODEL_PATH", APP_PATH . "models" . DS);       
        define("VIEW_PATH", APP_PATH . "views" . DS);
        
        define("CORE_PATH", WHITEPHP_PATH . "core" . DS);
        define('DB_PATH', WHITEPHP_PATH . "database" . DS);
        define("LIB_PATH", WHITEPHP_PATH . "libraries" . DS);
        define("HELPER_PATH", WHITEPHP_PATH . "helpers" . DS);
        define("SMARTY_PATH", ROOT . "vendor" . DS . "smarty" . DS . "smarty". DS);
        
        // Require modules
        require CONFIG_PATH . "modules.php";
        
        $routes = array();
        foreach($modules as $module=>$option) {
            array_push($routes, str_replace("/", "", $option["route"]));
        }
        
        if(in_array($_REQUEST['m'], $routes) && !empty($_REQUEST['m'])) {       

        // Use specified model   
        define("MODULE", isset($_REQUEST['m']) ? $_REQUEST['m'] : 'Index');
        define("CONTROLLER", isset($_REQUEST['c']) ? $_REQUEST['c'] : 'Index');
        define("ACTION", isset($_REQUEST['a']) ? $_REQUEST['a'] : 'index');
        }
        
        else {
            // Use home model
            define("MODULE", "home");
            define("CONTROLLER", isset($_REQUEST['m']) ? $_REQUEST['m'] : 'Index');
            define("ACTION", isset($_REQUEST['c']) ? $_REQUEST['c'] : 'index');
            
        }
        
        // Load core classes    
        require CORE_PATH . "Controller.class.php";
        require CORE_PATH . "Loader.class.php";
        //require CORE_PATH . "Model.class.php";
        
        
        // Load configuration file
        $GLOBALS['config'] = include CONFIG_PATH . "config.php";        

        session_start();    //start session
        
        // smarty template
        require_once(SMARTY_PATH . "libs" . DS . "Smarty.class.php");
        require_once(WHITEPHP_PATH . "libraries" . DS . "Template.class.php");
        
    }
    
// Autoloading
private static function autoload(){

    spl_autoload_register(array(__CLASS__,'load'));

}

// Define a custom load method
private static function load($classname){

    if (substr($classname, -10) == "Controller"){

        // Controller
        require_once CONTROLLER_PATH . MODULE . DS . "$classname.class.php"; 

    } elseif (substr($classname, -5) == "Model"){

        // Model
        require_once  MODEL_PATH . MODULE . DS . "$classname.class.php";
        
    }

}
    
    private static function dispatch() {
        
        // Instantiate the controller class and call its method
        
        $controller_name = CONTROLLER . "Controller";
        $action_name = ACTION . "Action";
        $controller = new $controller_name;
        $controller->$action_name();
              
        
    }

}