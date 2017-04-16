<?php
namespace whitephp\db;

class dbo extends \PDO {
    private $error;
    
    function __construct($config) {
        try {
            parent::__construct($config['db_type'].':host='.$config['db_host'].';dbname='.$config['db_name'],$config['db_username'],$config['db_password']);
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            $this->error = 'Error: '.$e->getMessage();
        }
    }
    
    function getError() {
        return $error;
    }
    
    
}