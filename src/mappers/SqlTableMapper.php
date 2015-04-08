<?php 
namespace PhpSimpleOrm;

class SqlTableMapper
{
  protected $dataMap;
  protected $silentMap;
  protected $tableName;
  protected $dbShard;
  protected $sqlEngine;
  protected $selectString = false;
  
  function __construct($dbShard = false)
  {
    if ($dbShard === false)
    {
      throw new \ErrorException('No Shard set in '.get_class($this));
    }
    $this->dbShard = $dbShard;
    $this->dataMap = new \PhpSimpleOrm\DataMap();
    $this->silentMap = new \PhpSimpleOrm\DataMap();
  }
  
  public function setDataMap($val)
  {
    $this->dataMap = $val;
  }
  
  public function setDebugName($val)
  {
    $this->debugName = $val;
  }
  
  public function setSilentMap($val)
  {
    $this->silentMap = $val;  
  }
  
  public function setDbShard($val)
  {
    $this->dbShard = $val;
  }
  
  
  public function setTableName($val)
  {
    $this->tableName = $val;
  }
  
  public function getShard()
  {
   return $this->dbShard;  
  }
  
  public function setSqlEngineProvider($val)
  {
    $this->sqlEngineProvider = $val;
  }
  
  final public function getSqlEngine()
  {
    if (!isset($this->sqlEngine))
    {
      $this->sqlEngine = $this->sqlEngineProvider->provide($this->dbShard);
    }
    return $this->sqlEngine;    
  }
    
  public function setProfiler($profiler)
  {
    $this->profiler = $profiler;
  }
  
  public function getProfiler()
  {
      return $this->profiler;    
  }
    
    

  protected function internalExistsById($id)
  {
    $sql = $this->getSqlEngine()->getSelectSqlForId($id, $this);
    $dbService = $this->getDbService();
    $resultSet = $dbService->query($sql, $this);

    if ($resultSet->getNumRows() > 1)
    {
      throw new \ErrorException('found too many in the database?');
    }
    
    return ($resultSet->getNumRows() === 1);
  }
            
    
            
              
  
  public function setSchemaUpdaterProvider($val)
  {
    $this->schemaUpdaterProvider = $val;
  }
  
  public function getSchemaUpdater()
  {
    if (!isset($this->schemaUpdater))
    {
      $this->schemaUpdater = $this->schemaUpdaterProvider->provide($this);
    }
    return $this->schemaUpdater;
  }
  
  final protected function hasSchemaUpdater()
  {
    return isset($this->schemaUpdaterProvider);  
  }
  
  final public function getSchemaUpdateQueries()
  {
    if ($this->hasSchemaUpdater())
    {
      return $this->getSchemaUpdater()->getSchemaUpdateQueries();
    }
    else 
    {
      return array();      
    }
  } 
  
  public function getDbProvider()
  {
    return $this->dbShard;
  }
  
  public function getDbService()
  {
    return $this->getSqlEngine()->getDbService($this->dbShard);
  }
  
  public function produceNewDbService()
  {
    $timer = $this->profiler->startTimer('prodducing explicit new db-service '.get_class($this));
    
    $returnValue =  $this->getSqlEngine()->produceDbService($this->dbShard);
    
    $timer->stop();
    
    return $returnValue;
  }
    
  
  public function getPreparedTableName()
  {
    return $this->dbShard->getTablePrefix().$this->tableName;
  }
  
  public function insertQuery($hash)
  {

    if (!is_object($this->getDbService()))
    {
      throw new Exception("hier bitte: ".$this->getPreparedTableName());
    }

    $timer = $this->profiler->startTimer('Astract Mapper in insert Query for '.$this->tableName);
    $sql = $this->getSqlEngine()->getInsertQuery($this->getPreparedTableName(), $hash);
    $timer->stop();


    $this->getDbService()->query($sql, $this);
    
  }
  
  public function updateQuery($hash,$where)
  {
    if ($where == "")
    {
      throw new Exception('Empty where-clause when trying to update.'); 
    }
    
    $sql = $this->getSqlEngine()->getUpdateQuery($this->getPreparedTableName(), $hash, $where);
    $this->getDbService()->query($sql, $this);
  }
    
  public function deleteQuery($where)
  {
    if ($where == "")
    {
      throw new Exception('Empty where-clause when trying to delete.'); 
    }
    
    //$sql = "DELETE FROM ".$this->getPreparedTableName($this->dbShard)." WHERE ".$where;
    $sql = $this->getSqlEngine()->getDeleteQuery($this->getPreparedTableName(), $where);
    $this->getDbService()->query($sql, $this);
  }
  
  public function existsColumn($column)
  {
    return ($this->dataMap->existsColumn($column) || $this->silentMap->existsColumn($column));
  }
  
  public function existsField($field)
  {
    return ($this->dataMap->existsField($field) || $this->silentMap->existsField($field));
  }
  
  public function getFieldForColumn($column)
  {
    if ($this->dataMap->existsColumn($column))
    {
      return $this->dataMap->getFieldForColumn($column);
    }
    else if ($this->silentMap->existsColumn($column))
    {
      return $this->silentMap->getFieldForColumn($column);
    } 
    else
    {
      throw new \ErrorException('did not find column '.$column);
    }
  }
  
  public function getColumnForField($field)
  {
    if ($this->dataMap->existsField($field))
    {
      return $this->dataMap->getColumnForField($field);
    }
    else if ($this->silentMap->existsField($field))
    {
      return $this->silentMap->getColumnForField($field);
    }
    else
    {
      throw new \ErrorException('did not find field '.$field);
    }
  }

  public function getPreparedColumnForField($field)
  {
    $column = $this->getColumnForField($field);
    return $this->getPreparedColumn($column);
    
  }

  protected function selectQuery($where)
  {
    $sql = $this->getSqlEngine()->assembleSelectQuery($where,$this);
    $resultSet = $this->getDbService()->query($sql, $this);
    return $resultSet;
  }
  
  public function getPreparedColumn($column)
  {
    return $this->getPreparedTableName().".".$column;
  }
  
  public function getColumns()
  {
    return $this->dataMap->getColumns();
  }

  public function getFields()
  {
    return $this->dataMap->getFields();
  }
    
  public function getSelectColumnsString()
  {
    if (!$this->selectString)
    {
      $items=array();
      foreach ($this->getColumns() as $column)
      {
        array_push($items, $this->getPreparedColumn($column));
      }
      
      $this->selectString = implode(", ", $items);
      
    }
    
    return $this->selectString;
  }
  
  public function getResponsibleMapperForField($field)
  {
    if ($this->existsField($field))
    {
      return $this;
    }
    else
    {
      throw new \ErrorException("coudl not find resposible mapper for field $field in ".$this->getPreparedTableName()." bad coding here in ".__FILE__);
    }
  }



      
}


  