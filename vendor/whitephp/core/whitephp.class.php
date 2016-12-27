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
        
        // rape $_GET for route params
        $_GET = explode('/', $_GET["whitephproute"]);
        
        if(in_array($_GET[0], $routes) && !empty($_GET[0])) {       

        // Multi Module Page: Use specified Module   
        define("MODULE", isset($_GET[0]) ? $_GET[0] : 'home');
        define("CONTROLLER", isset($_GET[1]) ? $_GET[1] : 'Index');
        define("ACTION", isset($_GET[2]) ? $_GET[2] : 'index');

        // only want params after route params in $_GET and $_REQUEST
        unset($_GET[0]);
        unset($_GET[1]);
        unset($_GET[2]);
        
        }
        
        else {
            // Single Module Page
            define("MODULE", "home");
            define("CONTROLLER", isset($_GET[0]) ? $_GET[0] : 'Index');
            define("ACTION", isset($_GET[1]) ? $_GET[1] : 'index');

            // only want params after route params in $_GET and $_REQUEST
            unset($_GET[0]);
            unset($_GET[1]);
            
        }
                
        // reorganize $_GET and also write into $_REQUEST
        $_GET = array_values($_GET);
        $_REQUEST = array_merge ($_REQUEST, $_GET);
        
        // Load core classes    
        require CORE_PATH . "Controller.class.php";
        require CORE_PATH . "Loader.class.php";
        //require CORE_PATH . "Model.class.php";
        
        
        // Load configuration file
        $GLOBALS['config'] = include CONFIG_PATH . "config.php";        

        session_start();    //start session
        
        // smarty template
        require_once(SMARTY_PATH . "libs" . DS . "Smarty.class.php");
        
        // whitephp classes
        require_once(WHITEPHP_PATH . "libraries" . DS . "http" . DS . "PostArray.class.php");
        require_once(WHITEPHP_PATH . "libraries" . DS . "view" . DS . "Template.class.php");
        
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