<?php
namespace PhpSimpleOrm;

class PostgreSqlConnector
{
	//
	
	public function __construct($dbConfig)
	{
		//
		$this->dbConnection = pg_connect("host=".$dbConfig->getDbHost()." port=".$dbConfig->getDbPort()." dbname=".$dbConfig->getDbName()." user=".$dbConfig->getDbUser()." password=".$dbConfig->getDbPassword()."  ", PGSQL_CONNECT_FORCE_NEW);	
		
	}
		
	public function getLastError()
	{
	 return pg_last_error($this->dbConnection);  
	}	
	
	public function getDebugName()
	{
		return "POSTGRESQL";	
	}	
	
  public function escapeString($string)
  {
    return pg_escape_string($this->dbConnection, $string);
  }
					
	public function query($sql)
	{
		try
		{
  		$result = pg_query ($this->dbConnection, $sql );
    }
    catch (\ErrorException $e)
    {
      $result=false;
    }  
      
    $postgreSqlResult = new PostgreSqlResult();
    $postgreSqlResult->setResult($result);
				
    if (!$result)
    {
      $postgreSqlResult->setEmpty();
      $postgreSqlResult->setError();
    }
    else
    {
      $postgreSqlResult->setFull();
    }
			
		
        
		return $postgreSqlResult;
	}		
	
	public function asyncQuery($sql)
	{
		//
		pg_send_query($this->dbConnection,$sql);
		$this->debugSql = $sql;
	}
		
	public function reapAsyncQuery()
	{
		//
		$postgreSqlResult = new PostgreSqlResult();
		
		$result = pg_get_result($this->dbConnection);
		if ($text=pg_result_error($result))
		{
		  error_log('error with postgresql in postgresqlconnector line 62: '.$text." from sql: ".$this->debugSql);
		  $postgreSqlResult->setError();
		}
    else 
    {
        
    }      
		
		if ($result === false)
		{
		  //  
      $postgreSqlResult->setEmpty();
    }  
    else
    {
      //
      $postgreSqlResult->setResult($result);
      $postgreSqlResult->setFull();
            
    }		
    
    return $postgreSqlResult;
        
	}
	
	
	
}
