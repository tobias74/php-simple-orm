<?php
namespace PhpSimpleOrm;

class MySqlConnector
{
	//
	
	public function __construct($dbConfig)
	{
	  try
	  {
	    $this->mysqli = new \mysqli(
        $dbConfig->getDbHost(),
        $dbConfig->getDbUser(),
        $dbConfig->getDbPassword(),
        $dbConfig->getDbName(),
        $dbConfig->getDbPort(),
        $dbConfig->getDbSocket()
      );
	  }
	  catch (\ErrorException $e)
	  {
	    error_log($e->getMessage());
	    error_log($dbConfig->getDbHost());
	    throw $e;
	  }
		
		
	}
	
  public function getLastError()
  {
   return $this->mysqli->error;  
  } 
		
	public function getDebugName()
	{
		return "MYSQL";	
	}	
	
  public function escapeString($string)
  {
    return $this->mysqli->real_escape_string($string);
  }
	  
	
	public function query($sql)
	{
		$result = $this->mysqli->query($sql);
		$mySqlResult = new MySqlResult();
		$mySqlResult->set_mysqli($this->mysqli);
		$mySqlResult->setResult($result);
    if (!$result)
    {
      $mySqlResult->setEmpty();
      $mySqlResult->setError();
    }
    else
    {
      $mySqlResult->setFull();
    }
		return $mySqlResult;
	}		
	
	public function asyncQuery($sql)
	{
		//
		$this->mysqli->query($sql, MYSQLI_ASYNC);
				
	}
		
	public function reapAsyncQuery()
	{
		//
		try 
		{
		  $result = $this->mysqli->reap_async_query();
      $mySqlResult = new MySqlResult();
      $mySqlResult->set_mysqli($this->mysqli);
      $mySqlResult->setResult($result);
      if (!$result)
      {
        $mySqlResult->setEmpty();
        $mySqlResult->setError();
      }
      else
      {
        $mySqlResult->setFull();
      }
		}
		catch (\ErrorException $e)
		{
      $mySqlResult = new MySqlResult();
      $mySqlResult->setEmpty();
		}
		
    return $mySqlResult;
				
	}
	
	
	
}
