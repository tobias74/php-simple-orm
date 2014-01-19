<?php 
namespace PhpSimpleOrm;

interface IDbResult
{
	public function fetchObject();
	public function close();
	public function getNumRows();
}

abstract class DbResult implements IDbResult
{
  //
  protected $isEmpty = true;
  protected $hasError = false;
  protected $result = -1;
  
  public function setEmpty()
  {
    $this->isEmpty = true;
  }
  
  public function isEmpty()
  {
   return $this->isEmpty;  
  }

  public function isFull()
  {
   return !$this->isEmpty;  
  }
  
  public function setError()
  {
    $this->hasError = true;  
  } 

  public function unsetError()
  {
    $this->hasError = false;  
  } 
     
  public function setFull()
  {
    $this->isEmpty=false;  
  }
  
  public function setResult($result)
  {
    $this->result = $result;
  }
  public function getResult()
  {
    return $this->result;
  }
  
  public function hasError()
  {
    return $this->hasError;
  }
        
}

class MySqlResult extends DbResult implements IDbResult
{
	public function set_mysqli($mysqli)
	{
	  $this->mysqli = $mysqli;
	}  
		
	public function fetchObject()
	{
	  if (!is_object($this->result))
	  {
	    throw new \ErrorException('do not fetch on non-object');
	  }
		return $this->result->fetch_object();
	}
	
  public function getAffectedRows()
  {
   return mysqli_affected_rows($this->mysqli);  
  }
		
	public function getNumRows()
	{
		return $this->result->num_rows;	
	}
	
	public function close()
	{
		$this->result->close();
	}
}


class PostgreSqlResult extends DbResult implements IDbResult
{
		
	public function fetchObject()
	{
	  if (!is_resource($this->result))
    {
      throw new \ErrorException('do not fetch on non-resource'.print_r($this->result,true));
    }
	  
		return pg_fetch_object($this->result);
	}

  public function getAffectedRows()
  {
    if ($this->result !== false)
    {
      return pg_affected_rows($this->result);  
    }
    else 
    {
      return -1;
    }
  }
		
	public function getNumRows()
	{
		return pg_num_rows($this->result);
	}
	
	public function close()
	{
		pg_close($this->result);
	}
	
}
