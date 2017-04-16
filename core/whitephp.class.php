<?php
namespace whitephp;

class whitephp {
    
    public static function run() {

        self::init();
        self::autoload();
        self::dispatch();
    
    }
    
    /**
     * 
     */
    private static function init() {
        
        // Define path constants
        
        define("DS", DIRECTORY_SEPARATOR);
        
        define("ROOT", getcwd() . DS);
        define("APP_PATH", ROOT . 'application' . DS);
        define("WHITEPHP_PATH", ROOT . "vendor" . DS . "whitephp" . DS . "whitephp" . DS);
                
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
        
        // Get json module configuration
        $config = json_decode(file_get_contents(CONFIG_PATH . "config.json.php"), true);
        
        if($config == false) {
            throw new \Exception('Your config.json file is invalid.');
        }        
        
        if(isset($config["site-url"])) {
            define('USER_PUBLIC_PATH', $config["site-url"] . "/public/");
        }
        else {
            define('USER_PUBLIC_PATH, "/"');
        }
        session_start();    //start session
        
        // smarty template
        require_once(SMARTY_PATH . "libs" . DS . "Smarty.class.php");
        
        // whitephp classes
        require_once(WHITEPHP_PATH . "libraries" . DS . "http" . DS . "PostHandler.class.php");
        require_once(WHITEPHP_PATH . "libraries" . DS . "view" . DS . "Template.class.php");
        require_once(WHITEPHP_PATH . "database" . DS . "db.class.php");
        require_once(WHITEPHP_PATH . "database" . DS . "dbLoader.class.php");
        
        // helper functions
        require HELPER_PATH . "helpers.inc.php";
        
        // Get json module configuration
        $modules = json_decode(file_get_contents(CONFIG_PATH . "modules.json.php"), true);
        
        if($modules == false) {
            throw new \Exception('Your modules.json file is invalid.');
        }
        
        $routes = array();
        foreach($modules as $module=>$option) {
            array_push($routes, str_replace("/", "", $option["route"]));
        }
        
        // rape $_GET for route params
        if(isset($_GET["whitephproute"])) {
        $_GET = explode('/', $_GET["whitephproute"]);
        }
        // need to support single / home and multiple modules    
        if(isset($_GET[0]) && in_array($_GET[0], $routes) && !empty($_GET[0])) {       
            
        // Multi Module Page   
        define("MODULE", isset($_GET[0]) ? $_GET[0] : 'home');
        
        if(isset($modules[MODULE]["version-controlled"]) && $modules[MODULE]["version-controlled"] == true) {
            // use versioning
            define("VERSION", isset($_GET[1]) ? $_GET[1] : 'Index');
            define("CONTROLLER", isset($_GET[2]) && !empty($_GET[2]) ? $_GET[2] : 'Index');
            define("ACTION", isset($_GET[3]) ? $_GET[3] : 'index');
            unset($_GET[3]);
        }
        
        else {
            define("CONTROLLER", isset($_GET[1]) && !empty($_GET[1]) ? $_GET[1] : 'Index');
            define("ACTION", isset($_GET[2]) ? $_GET[2] : 'index');
            
        }

        // only want params after route params in $_GET and $_REQUEST
        unset($_GET[0]);
        unset($_GET[1]);
        unset($_GET[2]);
        
        }
        
        else {
            // Single Module or home page
            
            // get home page
            define("MODULE", helpers\array_find_parent($modules, "route", "/"));
            
            define("CONTROLLER", isset($_GET[0]) && !empty($_GET[0]) ? $_GET[0] : 'Index');
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
             
                        
        // Register the secure session handler
        //session_set_save_handler(new \whitephp\session\SecureSession(), true);
        
    }
        
    // Autoloading
    private static function autoload()  {
    
        spl_autoload_register(array(__CLASS__,'load'));
    
    }
    
    /**
     * @param String $classname
     */
    private static function load($classname){
        if (substr($classname, -10) == "Controller"){
            
        
            if(isset($modules[MODULE]["version-controlled"]) && $modules[MODULE]["version-controlled"] == true) {
                // Controller
                if(file_exists(CONTROLLER_PATH . MODULE . VERSION . DS . "$classname.class.php")) {
                    require_once CONTROLLER_PATH . MODULE . VERSION . DS . "$classname.class.php";
                }
                else {
                    header("HTTP/1.0 404 Not Found");
                    echo "HTTP/1.0 404 Not Found";
                    echo "<br />(Controller not Found)";
                    exit;
                }            
             }
             else {
                // Controller
                if(file_exists(CONTROLLER_PATH . MODULE . DS . "$classname.class.php")) {
                    require_once CONTROLLER_PATH . MODULE . DS . "$classname.class.php";
                }
                else {
                    header("HTTP/1.0 404 Not Found");
                    echo "HTTP/1.0 404 Not Found";
                    echo "<br />(Controller not Found)";
                    echo "<br />" . CONTROLLER_PATH . MODULE . DS . "$classname.class.php";
                    exit;
                }           
            }
                
            
        } 
        elseif (substr($classname, -5) == "Model")  {
            
            // Model
            if($modules[MODULE]["version-controlled"] == true) {
                require_once  MODEL_PATH . MODULE . DS . "$classname.class.php";   
            } 
            
            else {
                require_once  MODEL_PATH . MODULE . DS . "$classname.class.php";
            }
        }
    
    }
    
    private static function dispatch() {
        
        // Instantiate the controller class and call its method
        
        $controller_name = CONTROLLER . "Controller";
        $action_name = ACTION . "Action";
        $controller = new $controller_name; 
        if(method_exists($controller, $action_name)) {
        $controller->$action_name();
        }
        else {
            header("HTTP/1.0 404 Not Found");
            echo "HTTP/1.0 404 Not Found";
            exit;            
        }
        
    }
    
    public static function jump($module_name = MODULE, $controller_name = CONTROLLER, $action_name = ACTION) {
        define(MODULE, $module_name);
        define(CONTROLLER, $controller_name);
        define(ACTION, $action_name);
        whitephp::dispatch();
    }

}