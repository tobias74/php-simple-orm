<?php 
namespace PhpSimpleOrm;

abstract class AbstractSqlEngine2013
{

  public function __construct($dbShard)
  {
    $this->dbShard = $dbShard;
    
  }
  
  
  public function setProfiler($profiler)
  {
    $this->profiler = $profiler;
  }
  
  protected function arrayAddSlashes($values)
  {
    $saveValues = array();
    foreach ($values as $value)
    {
      array_push($saveValues, addslashes($value));
    }
    return $saveValues;
  }
  
  
  protected function escapeString($string)
  {
    return $this->getDbService()->escapeString($string);
  }
  
  protected function escapeValues($values)
  {
    $timer = $this->profiler->startTimer('Astract SQL-Engine '.get_class($this).' escaping Values');
    
    $escapedValues = array();
    foreach ($values as $index => $value)
    {
      $escapedValues[$index] = $this->escapeString($value); 
    }
    
    $timer->stop();
    
    return $escapedValues;
  }
    
  public function getInsertQuery($tableName,$hash)
  {
    $columns = array_keys($hash);
    $values = array_values($hash);
    
    $sql = "INSERT INTO $tableName (".implode(",",$columns).") VALUES ('".implode("','",$this->escapeValues($values))."')";
    return $sql;    
  }             

  protected function preparePairsForUpdateQuery($hash)
  {
    $pairs = array();
    foreach($hash as $column => $value)
    {
      array_push($pairs, $column." = '".$this->escapeString($value)."' ");
    } 
    return $pairs;
  } 
            
  public function getUpdateQuery($tableName,$hash,$whereClause)
  {
    $pairs = $this->preparePairsForUpdateQuery($hash);
    if ($whereClause == '')
    {
      throw new Exception('empty where-clause.');
    }
    $sql = "UPDATE $tableName SET ".implode(",",$pairs)." WHERE ".$whereClause;
    return $sql;
  }
        
  public function getDeleteQuery($tableName,$whereClause)
  {
    if ($whereClause == '')
    {
      throw new Exception('empty where-clause.');
    }
    
    $sql = "DELETE FROM $tableName WHERE $whereClause";
    return $sql;
  }     
  
  abstract protected function getWhereClause($spec, $mapper);
  abstract protected function getLimitClause($spec, $mapper);
    
    
    

  public function getSelectStringWithAlias($table,$whitelist,$alias)
  {
    $items=array();

    foreach ($table->getColumns() as $column)
    {
      $field = $table->getFieldForColumn($column);
      if (array_search($field,$whitelist) !== false)
      {
        array_push($items, $alias.".".$column);
      }
    }
    
    $selectString = implode(", ", $items);
    
    return $selectString;
  }


  public function getSelectColumnsStringWithWhitelist($table,$whitelist)
  {
    $items=array();

    foreach ($table->getColumns() as $column)
    {
      $field = $table->getFieldForColumn($column);
      if (array_search($field,$whitelist) !== false)
      {
        array_push($items, $table->getPreparedColumn($column)); //.' AS '.$column);
      }
    }
    
    $selectString = implode(", ", $items);
    
    return $selectString;
  }


  public function getSelectColumnsStringWithoutBlacklist($table,$blacklist)
  {
    $items=array();

    foreach ($table->getColumns() as $column)
    {
      $field = $table->getFieldForColumn($column);
      if (array_search($field,$blacklist) === false)
      {
        array_push($items, $table->getPreparedColumn($column)); //.' AS '.$column);
      }
      else 
      {
        // dont use, this is blacklisted.
      }
    }
    
    $selectString = implode(", ", $items);
    
    return $selectString;
  }


  public function getSelectColumnsString($table)
  {
    $items=array();

    foreach ($table->getColumns() as $column)
    {
      array_push($items, $table->getPreparedColumn($column)); //.' AS '.$column);
    }
    
    $selectString = implode(", ", $items);
    
    return $selectString;
  }


              
  public function getSelectQueryForSpecification($spec, $mapper)
  {
    $whereClause = $this->getWhereClause($spec, $mapper);
    $orderClause = $spec->getOrderClause($mapper);
    $limitClause = $this->getLimitClause($spec, $mapper);
    
    if ($limitClause == "")
    {
      $limitClause = "LIMIT 1000";
    }
    
    $sql = "SELECT ".$mapper->getSelectColumnsString()." FROM ".$mapper->getPreparedTableName()." $whereClause $orderClause $limitClause ";
    return $sql;
  }
  
  public function getCountQuery($criteria, $mapper)
  {
    $whereClause = " WHERE ".$criteria->getWhereClause($mapper);
    $sql = "SELECT COUNT(*) as countRows FROM ".$mapper->getPreparedTableName()." $whereClause ";
    return $sql;
  }
  
  public function getSelectSqlForId($id, $mapper)
  {
    $sql = "SELECT ".$mapper->getSelectColumnsString()." FROM ".$mapper->getPreparedTableName()." WHERE ".$mapper->getPreparedColumnForField('id')."='".addslashes($id)."'";
    return $sql;  
  }
  
  public function getConjunctionClause($assoc)
  {
    $columns = array_keys($assoc);
    $values = $this->arrayAddSlashes(array_values($assoc));
    
    $pairs=array();
    foreach ($assoc as $column => $value)
    {
      $pairs[] = $column."='".$value."'";
    }
    
      $where = implode(" AND ", $pairs);
    return $where;    
  }
  
  public function assembleSelectQuery($where,$table)
  {
    //  
    if ($where!="")
    {
      $where = " WHERE ".$where;
    }
    
    $sql = "SELECT ".$this->getSelectColumnsString($table)." FROM ".$table->getPreparedTableName()." ".$where;
    
    return $sql;
  }
  
  public function getInClause($column,$valueList)
  {
    $implodedString = implode("','", $valueList);
    $inText = " $column IN ('".$implodedString."') ";
    return $inText;
  }
  
  
  public function getSchemaUpdateQueries($mapper)
  {
    return array();
  } 
}
      
    
  