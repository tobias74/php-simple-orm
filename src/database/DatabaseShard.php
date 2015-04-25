<?php 
namespace PhpSimpleOrm;

interface IDatabaseShard
{
	public function getId();
	public function getTablePrefix();
	public function getMySqlService();
}


abstract class DatabaseShard extends DomainObject implements IDatabaseShard
{
	//
	
	
  public function getDebugName()
  {
    return "I am a DatasbeShard ".get_class($this);
  }
		
	public function setProfiler($profiler)
	{
		if (!is_object($profiler))
		{
			throw new \ErrorException('shard profiler is not an object?'.$profiler);
		}
		
		$this->profiler=$profiler;
	}
		
	public function getProfiler()
	{
		return $this->profiler;
	}
		
	public function setMySqlConnectorProvider($provider)
	{
		$this->mySqlConnectorProvider = $provider;
	}
			
	public function getMySqlConnectorProvider()
	{
		return $this->mySqlConnectorProvider;
	}

	public function setPostgreSqlConnectorProvider($provider)
	{
		$this->postgreSqlConnectorProvider = $provider;
	}
			
	public function getPostgreSqlConnectorProvider()
	{
		return $this->postgreSqlConnectorProvider;
	}
	
	public function setDbServiceProvider($provider)
	{
		$this->dbServiceProvider = $provider;
	}
			
	public function getDbServiceProvider()
	{
		return $this->dbServiceProvider;
	}
	
  public function produceMySqlService()
  {
    $dbConnector = $this->getMySqlConnectorProvider()->provide($this->getMySqlConfig());
    $dbService = $this->getDbServiceProvider()->provide($dbConnector);
    //$dbService->setSchemaUpdaterProvider($this->mySqlSchemaUpdaterProvider->curry($this));
    return $dbService;
  }
    
  public function producePostgreSqlService()
  {
    $dbConnector = $this->getPostgreSqlConnectorProvider()->provide($this->getPostgreSqlConfig());
    $dbService = $this->getDbServiceProvider()->provide($dbConnector);
    //$dbService->setSchemaUpdaterProvider($this->postgreSqlSchemaUpdaterProvider->curry($this));
    return $dbService;
  }
		
  public function getMySqlService()
  {
    if (!$this->mySqlService)
    {
      $this->mySqlService = $this->produceMySqlService();
    }
    return $this->mySqlService;
  }

  public function getPostgreSqlService()
  {
    if (!$this->postgreSqlService)
    {
      $this->postgreSqlService = $this->producePostgreSqlService();
    }
    return $this->postgreSqlService;
  }

  public function getMongoDbService()
  {
    if (!$this->mongoDbService) 
    {
        $this->mongoDbService = $this->produceMongoDbService();
    }
    return $this->mongoDbService;
  }
  
  protected function produceMongoDbService()
  {
    $mongoConnection = $this->getMongoClient();
    
    $mongoDbConfig = $this->getMongoDbConfig();
    $dbName = $mongoDbConfig->dbName;
    
    return $mongoConnection->$dbName;
  }							

  public function getMongoDB($subBase='')
  {
    $mongoConnection = $this->getMongoClient();
    $mongoDbConfig = $this->getMongoDbConfig();
    $db = $mongoConnection->selectDB($mongoDbConfig->dbName.$subBase);
    return $db;
  }
	
  protected function getMongoClient()
  {
    $mongoDbConfig = $this->getMongoDbConfig();
    
    if ($mongoDbConfig->serverUrl !== '')
    {
       $mongoConnection = new \MongoClient($mongoDbConfig->serverUrl);
    }
    else
    {
        $mongoConnection = new \MongoClient();
    }
    
    return $mongoConnection;    
  }
  
  
}

class MasterDatabaseShard extends DatabaseShard implements IDatabaseShard
{
	protected $mySqlService = false;
	protected $postgreSqlService;
	protected $mongoDbService;
  protected $neo4jService;
  protected $hbaseTablePrefix;
	


  public function setConfig($val)
  {
    $this->config = $val; 
  }
	


	public function getTablePrefix()
	{
		return $this->config->getDatabaseTablePrefix();
	}
	
	
	public function setProfiler($profiler)
	{
		$this->profiler = $profiler;
	}
	
    public function getProfiler()
    {
        return $this->profiler;    
    }
    
	
  protected function getDbConfig()
  {
    return $this->config->getDbConfig();
  }
  
	protected function getMySqlConfig()
	{
	  return $this->getDbConfig()->getMySqlConfig();
	}
	
  protected function getPostgreSqlConfig()
  {
    return $this->getDbConfig()->getPostgreSqlConfig();
  }
  
	protected function getMongoDbConfig()
	{
    return $this->getDbConfig()->getMongoDbConfig();	  
	}
  
  public function getHbaseTablePrefix()
  {
   return $this->getDbConfig()->getHbaseTablePrefix();  
  }

  public function getRiakBucketPrefix()
  {
    // we will use the same as with hbase. 
    return $this->getDbConfig()->getHbaseTablePrefix();  
  }
  
  public function getHbaseCLient()
  {
    //
    $socket = new \Thrift\Transport\TSocket ('localhost', 9090);
    $socket->setSendTimeout (2000); 
    $socket->setRecvTimeout (4000); 
    $transport = new \Thrift\Transport\TBufferedTransport ($socket);
    $protocol = new \Thrift\Protocol\TBinaryProtocol ($transport);
    $client = new \Hbase\HbaseClient ($protocol);
    
    $transport->open();
    
    return $client;        
    
  }        


  public function getRedisClient()
  {
    $redis = new \Predis\Client(array(
      'scheme' => 'tcp',
      'host' => $this->config->getRedisHost(),
      'port' => 6379
    ));
    
    return $redis;
  }


  public function getRiakClient()
  {
    //
    $client = new \Basho\Riak\Riak('127.0.0.1', 8098);    
    return $client;        
    
  }        
  			
		public function getNeo4jService()
    {
      if ($this->neo4jService == false)
      {
        $this->neo4jService = new \Everyman\Neo4j\Client();
      }
      
      return $this->neo4jService;
    }
}




class Shard extends DatabaseShard implements IDatabaseShard
{
  protected $mySqlService = false;
  protected $postgreSqlService = false;


  public $dbTablePrefix;
    
  public $dbHost;
  public $dbUser;
  public $dbPassword;
  public $dbName;
  public $dbSocket;
  public $dbPort;
  
  public $postgreSqlHost;
  public $postgreSqlUser;
  public $postgreSqlPassword;
  public $postgreSqlDbName;
  public $postgreSqlSocket;
  public $postgreSqlTablePrefix;
  public $postgreSqlPort;
  
  public $pathForFiles;
  
  
  
  public function getTablePrefix()
  {
    return $this->dbTablePrefix;
  }

  public function getDebugName()
  {
    return "I am a shard";
  }
  

  
  public function getPathForFiles()
  {
    return $this->pathForFiles;
  }
  
  public function setPathForFiles($val)
  {
    $this->pathForFiles = $val;   
  }

  protected function getMySqlConfig()
  {
    $dbConfig = new DbConfig();
    $dbConfig->setDbHost($this->dbHost);
    $dbConfig->setDbUser($this->dbUser);
    $dbConfig->setDbPassword($this->dbPassword);
    $dbConfig->setDbName($this->dbName);
    $dbConfig->setDbPort($this->dbPort);
    $dbConfig->setDbSocket($this->dbSocket);
    return $dbConfig;
    
  }
  protected function getPostgreSqlConfig()
  {
    $dbConfig = new DbConfig();
    $dbConfig->setDbHost($this->postgreSqlHost);
    $dbConfig->setDbUser($this->postgreSqlUser);
    $dbConfig->setDbPassword($this->postgreSqlPassword);
    $dbConfig->setDbName($this->postgreSqlDbName);
    $dbConfig->setDbPort($this->postgreSqlPort);
    $dbConfig->setDbSocket($this->postgreSqlSocket);
    
    return $dbConfig;  
    
  }
  
    
  
        
}



