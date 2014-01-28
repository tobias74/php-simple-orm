<?php 
namespace PhpSimpleOrm;


abstract class AbstractStrategizedRepository extends AbstractRepository
{
    //
    
  public function setCriteriaMaker($cf)
  {
    $this->criteria = $cf;
  }
  
    public function setWriteStrategy($strategy)
    {
      $this->writeStrategy = $strategy;
    }   
    
    public function getWriteStrategy()
    {
      return $this->writeStrategy;
    }   

    public function setReadStrategy($strategy)
    {
      $this->readStrategy = $strategy;
    }   
    
    public function getReadStrategy()
    {
      return $this->readStrategy;
    }   
    
          
    public function update($entity)
    {
        $this->getWriteStrategy()->update($entity);        
        return $entity;
    }

    public function insert($entity)
    {
        $this->getWriteStrategy()->insert($entity);        
        return $entity;
    }
    
    
    public function delete($entity)
    {
        $this->getWriteStrategy()->delete($entity);        
    }
    
    public function release($station)
    {
      $this->getReadStrategy()->release($station);  
    }
    

    public function getById($entityId, $userId = null)
    {
      if ($userId == null)
      {
        return $this->getSoleMatch(new \VisitableSpecification\Specification( $this->criteria->hasId($entityId) ) );
      }
      else
      {
        return $this->getSoleMatch(new \VisitableSpecification\Specification($this->criteria->hasId($entityId) -> logicalAnd($this->criteria->belongsToUser($userId))));
      }
    }
    
    public function existsId($entityId, $userId = null)
    {
      try
      {
        $item = $this->getById($entityId, $userId);
        return true;
      }
      catch (NoMatchException $e)
      {
        return false;
      }
    }
    

    public function getAll()
    {
      return $this->getBySpecification(new \VisitableSpecification\Specification( $this->criteria->any() ) );
    }
        
    
    final public function getBySpecification($spec)
    {
      $entities = $this->getReadStrategy()->getBySpecification($spec);
      return $entities;
    }



        

    
    protected function orderEntities($entities, $orderer)
    {
      return $entities; 
    }

    protected function limitEntities($entities, $limiter)
    {
      $entities = array_slice($entities, $limiter->getOffset(), $limiter->getLength());
      return $entities; 
    }
        
  
  
          
    
    
    
}


class StrategizedRepository extends AbstractStrategizedRepository
{


}


