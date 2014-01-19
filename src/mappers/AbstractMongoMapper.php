<?php 
namespace PhpSimpleOrm;

abstract class AbstractMongoMapper
{
    function __construct($dbShard)
    {
        //        
          
        $this->dbShard = $dbShard;
            
        $this->mongoDb = $this->dbShard->getMongoDbService();    
        
        $this->declareDataMap();
        $this->declareCollectionName();
        
        $this->ensureIndexes();
        
    }
       
  public function setProfiler($profiler)
  {
    if (!is_object($profiler))
    {
      throw new \ErrorException('profiler is not an object?'.$profiler);
    }
    $this->profiler = $profiler;
  }
               
    protected function ensureIndexes()
    {
      
    }
    
    
    public function getDebugName()
    {
      return "abstract mpongo mapper";
    }
    
    protected function getHashForEntity($entity)
    {
        $contentHash = array();
        
        foreach($this->dataMap->getFields() as $field)
        {
            $contentHash[$this->getColumnForField($field)] = $entity->getDryValue($field);
        }
        
        return $contentHash;
    }
    
    public function getColumnForField($field)
    {
        return $this->dataMap->getColumnForField($field);
    }
    
    public function getFieldForColumn($column)
    {
      return $this->dataMap->getFieldForColumn($column);
    }
            
    

    public function insert($entity)
    {
        $collectionName = $this->collectionName;
        $collection = $this->mongoDb->$collectionName;
        $hash = $this->getHashForEntity($entity);
        $collection->insert($hash);
    }
    
    public function update($entity)
    {
        $collectionName = $this->collectionName;
        
        $collection = $this->mongoDb->$collectionName;
        $hash = $this->getHashForEntity($entity);        
        
        $where = array('id' => $entity->getId());
        $collection->update($where, $hash, array('upsert'=>true));
        
    }
    
    public function delete($entity)
    {
        $collectionName = $this->collectionName;
        
        $collection = $this->mongoDb->$collectionName;
        $hash = $this->getHashForEntity($entity);        
        
        $where = array('id' => $entity->getId());
        $collection->remove($where, array('justOne'));
      
      
    }
            
    public function getDistinctFieldBySpecification($field,$spec)
    {
      $whereArray = $this->getWhereArray($spec);
      $limit = $spec->getLimit();
      $offset = $spec->getOffset();
      
      $finalResult = array();      

      do
      {
        $timer = $this->profiler->startTimer('mongo-mapper, getting distincts in '.$this->collectionName);
        
        $resultSet = $this->getCollection()->aggregate(array(
          array('$match' => $whereArray),
          array('$limit' => $limit+$offset),
          array('$group' => array(
            '_id' => '$'.$this->getColumnForField($field)
          )),
        ));
        
              
        if (isset($resultSet['errmsg']))
        {
          throw new \ErrorException($resultSet['errmsg']);
        }      
  
        foreach($resultSet['result'] as $index => $result)
        {
          $finalResult[] = $result['_id'];
          $whereArray = array('$and' => array($whereArray, array($this->getColumnForField($field) => array('$ne'=>$result['_id']))));  
        }
        
        $timer->stop();
      }
      while ((count($finalResult)<($limit+$offset)) && (isset($resultSet['result']) && (count($resultSet['result']) > 0)  ));
      
      $finalResult = array_slice($finalResult,$offset,$limit);
            
      if (count($finalResult) != count(array_unique($finalResult)) )
      {
        throw new \ErrorException('why too many?');
      }
            
      return $finalResult;
    }
    
    
    public function getBySpecification($spec)
    {
      $whereArray = $this->getWhereArray($spec);
      $resultSet = $this->getCollection()->find($whereArray)->limit($spec->getLimit())->skip($spec->getOffset());
      $return = $this->loadAllFromResultSet($resultSet);      
      return $return;
    }
                        

    protected function getCollection()
    {
      $collectionName = $this->collectionName;
      $collection = $this->mongoDb->$collectionName;
      return $collection;      
    }
        
    protected function getWhereArray($spec)
    {
      if ($spec->hasCriteria())
      {
        $whereArrayMaker = new MongoWhereArray($this);
        $spec->getCriteria()->acceptVisitor($whereArrayMaker);
        $whereArray = $whereArrayMaker->getArrayForCriteria($spec->getCriteria());
        
        //error_log(json_encode($whereArray));
      }
      else 
      {
        $whereArray=array();  
      }
      
      return $whereArray;
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
      
      
      $return = $this->getBySpecification($spec);
      
      return $return;
    }

                                                    
    protected function loadAllFromResultSet($resultSet)
    {
      
        $entities = array();
        
        $timer = $this->profiler->startTimer('MongoDBMapper rewind resultset '.$this->collectionName);
        $resultSet->rewind();
        $timer->stop();
        
        while ($resultSet->valid())
        {
          $index = $resultSet->key();
          $document = $resultSet->current();
                    
          $entities[] = $this->reinstantiateEntityFromDocument($document);
         
          $resultSet->next();
        }
         
        /*      
        foreach ($resultSet as $index => $document)
        {
          //
            $timer2 = $this->profiler->startTimer('MongoDBMapper Reinstatiate Entity '.$index.' (INSIDE) in '.$this->collectionName);
                    
            $entities[] = $this->reinstantiateEntityFromDocument($document);
            $timer2->stop();
                        
        }
        */

        
        return $entities;
    }

    protected function reinstantiateEntityFromDocument($document)
    {
        $entity = $this->produceEmptyEntity();
        
        
        foreach ($this->dataMap->getFields() as $field)
        {
          $column = $this->getColumnForField($field);
          $value = $document[$column];
          $entity->injectValue($field, $value);
                    
        }        
                
        return $entity;
    }
                                        
     
    
    
        
}


