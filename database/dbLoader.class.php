<?php
namespace whitephp\db;

class dbLoader {
    protected $dsn;
    protected $username;
    protected $password;
    protected $configid;
    protected $config;
    
    function __construct($configid)  {
        
        $this->configid = $configid;
        
        // Get json db configuration
        $dbconfigs = json_decode(file_get_contents(CONFIG_PATH . "db.json.php"), true);
        $this->config = $dbconfigs[$this->configid];
        $this->dsn = $dbconfigs[$this->configid]["db_type"].':host='.$dbconfigs[$this->configid]["db_host"].';dbname='.$dbconfigs[$this->configid]["db_name"];
        $this->username = $dbconfigs[$this->configid]["db_username"];
        $this->password = $dbconfigs[$this->configid]["db_password"];           
    
    }
    
    function connect() {
        $dbo = new dbo($this->config);
        return $dbo;
    }
    
}