<?php 
namespace PhpSimpleOrm;


class CompleteConfig
{
	public function __construct()
	{
		
	}
		
	public function setMySqlConfig($config)
	{
		$this->mySqlConfig = $config;
	}	

	public function setPostgreSqlConfig($config)
	{
		$this->postSqlConfig = $config;
	}	

	public function getMySqlConfig()
	{
		return $this->mySqlConfig;
	}	

	public function getPostgreSqlConfig()
	{
		return $this->postSqlConfig;
	}	
	
	public function getMongoDbConfig()
	{
	    return $this->mongoDbConfig;
	}
	
	public function setMongoDbConfig($config)
	{
	    $this->mongoDbConfig = $config;
	}

  public function setHbaseConfig($config)
  {
    $this->hbaseConfig = $config;
  } 
			
	public function getHbaseTablePrefix()
	{
	  return $this->hbaseConfig->tablePrefix;
	}		
}

class DbConfig
{
	protected $dbHost;
	protected $dbSocket;
	protected $dbUser;
	protected $dbPassword;
	protected $dbName;
	protected $dbPort;

	public function getDbHost()
	{
		return $this->dbHost;
	}
	
	public function getDbSocket()
	{
		return $this->dbSocket;
	}
	
	public function getDbUser()
	{
		return $this->dbUser;
	}
	
	public function getDbPassword()
	{
		return $this->dbPassword;
	}
	
	public function getDbName()
	{
		return $this->dbName;
	}
	
	public function getDbPort()
	{
		return $this->dbPort;
	}

	
	public function setDbHost($val)
	{
		$this->dbHost = $val;
	}
	
	public function setDbSocket($val)
	{
		$this->dbSocket = $val;
	}
	
	public function setDbUser($val)
	{
		$this->dbUser = $val;
	}
	
	public function setDbPassword($val)
	{
		$this->dbPassword = $val;
	}
	
	public function setDbName($val)
	{
		$this->dbName = $val;
	}
	
	public function setDbPort($val)
	{
		 $this->dbPort = $val;
	}
	
}

class MongoDbConfig
{
    public $serverUrl;
    public $dbName;
}

class HbaseConfig
{
    public $tablePrefix;
    
}
