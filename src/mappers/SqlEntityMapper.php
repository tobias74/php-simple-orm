<?php 
namespace PhpSimpleOrm;

class SqlEntityMapper extends SqlTableMapper
{
  protected $hasStartedAsync=false;
  protected $debugName = 'sql entity mapper';
    
  public function setEntityProvider($val)
  {
    $this->entityProvider = $val;
  }
  public function produceEmptyEntity()
  {
    $entity = $this->entityProvider->provide();
    $entity->setShardId($this->getShardId());
    return $entity;
  }
  
    
  public function setReadRecorder($recorder)
  {
    $this->readRecorder = $recorder;
  }
  
  
  public function getDebugName()
  {
   return $this->debugName;  
  }
  
  public function setDebugName($val)
  {
    $this->debugName = $val;
  }
  
  protected function reinstantiateNakedEntityFromRow($row)
  {
    $entity = $this->produceEmptyEntity();
   
    foreach($this->getFields() as $field)
    {
      $columnName = $this->getColumnForField($field);
      $entity->injectValue($field, $row->$columnName);
    } 

    if (isset($this->readRecorder))
    {
      $this->readRecorder->recordRead($entity);
    }
    
    return $entity;
  }

  public function release($entity)
  {
    if (isset($this->readRecorder))
    {
      $this->readRecorder->unRecord($entity);  
    }
    
  } 
   

    
    
  protected function loadAllFromResultSet($resultSet)
  {
    $timer = $this->getProfiler()->startTimer('Loading All From ResultSet: '.$this->getPreparedTableName().' used for '.get_class($this->getSqlEngine()).'');
  
    $entities = array();
    while ($row = $resultSet->fetchObject())
    {
      $timer2 = $this->getProfiler()->startTimer('Inside LAR: '.$this->getPreparedTableName().' used for '.get_class($this->getSqlEngine()).'');
      $entities[] = $this->reinstantiateNakedEntityFromRow($row);
      $timer2->stop();
    }

    $timer->stop();
    
    return $entities;
  }
    
  public function delete($entity)
  {
    $where = "id='".addslashes($entity->getId())."'";
    $this->deleteQuery($where);
  }

  
  protected function getRowHashForEntity($entity)
  {
    $contentHash = array();
    foreach($this->dataMap->getFields() as $field)
    {
      $contentHash[$this->getColumnForField($field)] = $entity->getDryValue($field);
    }
    return $contentHash;
  }
        
  public function insert($entity)
  {
    $hash = $this->getRowHashForEntity($entity);
    $this->insertQuery($hash);
    $entity->setShardId($this->getShardId());
  }
        
  public function update($entity)
  {
    // since we want to be able to plug in new databases, every mapper has to make this check
    if (!$this->internalExistsById($entity->getId())) 
    {
      //throw new \ErrorException('inconsitency between mysql and postgresql. '.$entity->getId());
      error_log('we want to update, but is not here.'.get_class($this));
      //$this->insert($entity);
    }
    else
    {
      $hash = $this->getRowHashForEntity($entity);
      $where = $this->getPreparedColumnForField('id')."='".addslashes($entity->getId())."'";
      $this->updateQuery($hash,$where);
    }
  }
  
  
  public function startAsyncQueryForSpecification($spec)
  {
    if ($this->hasStartedAsync)
    {
      die('double started async operations! in abstract mapper');
    }
    
    $this->hasStartedAsync = true;
    $sql = $this->generateSqlForSpecification($spec);
    
    $this->asyncDbService = $this->produceNewDbService();
    $this->asyncDbService->asyncQuery($sql, $this);
    return $this->asyncDbService;
  }
  
  public function getEntitiesFromAsyncQuery($dbService)
  {
    if ($this->asyncDbService !== $dbService)
    {
      die('wrong we did not get our own dbService back');
    }
    
    $entities = array();
    $time = time(); 
    $resultSet = $dbService->reapAsyncQuery();
    
    while ($resultSet->isFull() && ($time + 10 > time()))
    {
      $myEntities = $this->loadAllFromResultSet($resultSet);
      $entities = array_merge($entities, $myEntities);  
      
      $resultSet = $dbService->reapAsyncQuery();
    } 
        
    $this->hasStartedAsync=false;
    return $entities;
  }
  
  public function getByIds($ids)
  {
    $criteriaMaker = new CriteriaMaker();
    if (!is_array($ids))
    {
      $criteria = $criteriaMaker->hasId($ids);
    }
    else
    {
      $criteria = $criteriaMaker->none();
      foreach ($ids as $id)
      {
        $criteria = $criteria->logicalOr($criteriaMaker->hasId($id));  
      }
    }
    $spec = new Specification($criteria);
    return $this->getBySpecification($spec);
  }
  
  public function getBySpecification($spec)
  {
    $sql = $this->generateSqlForSpecification($spec);   
    $timer = $this->profiler->startTimer('in abstract entity mapper getting the db-service');
    $dbService = $this->getDbService();
    $timer->stop();
    
    $resultSet = $dbService->query($sql, $this);
    $this->debugSql = $sql;
    return $this->loadAllFromResultSet($resultSet);
  }


  public function getBySpecificationList($specList)
  {
    $sql = $this->generateSqlForSpecificationList($specList);   
    $timer = $this->profiler->startTimer('in abstract entity mapper getting the db-service');
    $dbService = $this->getDbService();
    $timer->stop();
    
    $resultSet = $dbService->query($sql, $this);
    $this->debugSql = $sql;
    return $this->loadAllFromResultSet($resultSet);
  }
  
  protected function generateSqlForSpecification($spec)
  {
    $sql = $this->getSqlEngine()->getSelectQueryForSpecification($spec, $this);
    return $sql;
  }

  protected function generateSqlForSpecificationList($specList)
  {
    $sql = $this->getSqlEngine()->getSelectQueryForSpecificationList($specList, $this);
    return $sql;
  }
  
  public function countByCriteria($criteria)
  {
    $sql = $this->getSqlEngine()->getCountQuery($criteria,$this);

    $dbService = $this->getDbService();
    $resultSet = $dbService->query($sql, $this);
    $row = $resultSet->fetchObject();
    return $row->countRows;
  }
  
  
  protected function getFirstOnly($array)
  {
    if (count($array) == 0)
    {
      throw new ZeitfadenNoMatchException("Did not find any in ".get_class($this));
    }
    elseif(count($array) > 1)
    {
      $item = array_shift($array);
      throw new ErrorException("Nahh found too many(".(count($array)+1).")... with ".$item->getId());
    }
    else
    {
      return array_shift($array);
    }
  }
  
  public function getSoleMatch($spec)
  {
    $items = $this->getBySpecification($spec);
    error_log('we have how much items '.count($items));
    return $this->getFirstOnly($items);   
  }
    
  
}


