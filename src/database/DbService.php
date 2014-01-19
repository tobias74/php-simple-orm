<?php 
namespace PhpSimpleOrm;


class DbService
{
	protected $dbConnector = false;
	
	
	public function getDbConnector()
	{
		return $this->dbConnector;	
	}
	
	public function __construct($dbConnector)
	{
		$this->dbConnector = $dbConnector;
		$this->inAsyncQuery = false;
	}
	
	public function setProfiler($profiler)
	{
		if (!is_object($profiler))
		{
			throw new \ErrorException('profiler is not an object?'.$profiler);
		}
		$this->profiler = $profiler;
	}
	
	public function escapeString($string)
	{
    $timer = $this->profiler->startTimer('Escaping Strings in DBService used for '.get_class($this->getDbConnector()).'');
	  $returnValue = $this->getDbConnector()->escapeString($string);
    $timer->stop();
    return $returnValue;
	}
	
	public function query($sql,$mapper)
	{
		// comment this out, when schema schanges are done.
		//$this->workSchemaChanges($mapper);

		$this->_sqlInCaseOfSchemaUpdates = $sql;
				
		$timer = $this->profiler->startTimer(substr($sql,0,100).' used for '.get_class($this->getDbConnector()).'');
		$result = $this->getDbConnector()->query($sql);
		$timer->stop();
		
		if ($result->hasError())
		{
			$this->workSchemaChanges($mapper);
			$result = $this->getDbConnector()->query($sql);
			if ($result->hasError())
			{
			  
        die('ERROR: '.$sql.'used for '.get_class($this->getDbConnector()));
			  error_log('ERROR: '.$sql.'used for '.get_class($this->getDbConnector()));
				throw new \ErrorException('Wrongggg query in Engine: '.$this->getDbConnector()->getDebugName().' '.$sql.' returned ERROR: '.$this->getDbConnector()->getLastError());
				//throw new Exception('Wrong query? '.$sql);
			}
		}
		return $result;
	}

	
	public function asyncQuery($sql,$mapper)
	{
		// comment this out, when schema schanges are done.
		//$this->workSchemaChanges($mapper);
		if ($this->inAsyncQuery)
		{
			die('cannot restart another async query in DatabaseServices.');
		}
		
		$this->inAsyncQuery = true;
		
		$this->_sqlInCaseOfSchemaUpdates = $sql;
    $this->_mapperInCaseOfSchemaUpdates = $mapper;
		
		
		//$this->getDbConnector()->query($sql, MYSQLI_ASYNC);
		$this->getDbConnector()->asyncQuery($sql);
				
		
	}
	
	
	
	public function reapAsyncQuery()
	{
		$result = $this->getDbConnector()->reapAsyncQuery();
		
		$this->inAsyncQuery = false;
		
		if ($result->hasError())
		{
		  error_log(print_r($result,true));
		  error_log('ERROR: '.$this->_sqlInCaseOfSchemaUpdates.'used for '.get_class($this->getDbConnector()));
		    
		  
		  error_log('Working Schema-Chnages, for error with '.$this->_sqlInCaseOfSchemaUpdates);
      
			$this->workSchemaChanges($this->_mapperInCaseOfSchemaUpdates);
			$result = $this->getDbConnector()->query($this->_sqlInCaseOfSchemaUpdates);
			if ($result->hasError())
			{
			  error_log($result->getResult());
				throw new \ErrorException('Wrongggg query in async? '.$this->_sqlInCaseOfSchemaUpdates.' returned ERROR: '.$result->getResult());
				//throw new Exception('Wrong query? '.$sql);
			}
		}

		return $result;
		
	}
	
	protected function workSchemaChanges($mapper)
	{
	  error_log('Working '.count($mapper->getSchemaUpdateQueries()).' Schema-Chnages for '.get_class($this->getDbConnector()).' within mapper '.$mapper->getDebugName());
	  
		foreach($mapper->getSchemaUpdateQueries() as $sql)
		{
			$result = $this->getDbConnector()->query($sql);
			if ($result->hasError())
			{
			  error_log("shipwrecked with $sql");
				//echo "<br><br>shipwrecked with $sql";
				//throw new \Exception("shipwrecked with $sql");
				//nah we dont catch errors here. just roll on
			}
		}
	}
}
