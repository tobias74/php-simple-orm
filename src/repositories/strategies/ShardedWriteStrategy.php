<?php 
namespace PhpSimpleOrm;



class ShardedWriteStrategy extends AbstractShardedStrategy
{
  //
    
        
  public function update($entity)
  {
      if ($entity->getUserId() == false)
      {
          throw new \Exception("no user Id for entity???");
      }

      $this->getShardedMapperByUserId($entity->getUserId())->update($entity);        

      
      return $entity;
  }

  public function insert($entity)
  {
    $mapper = $this->getShardedMapperByUserId($entity->getUserId());
    $mapper->insert($entity);        
    return $entity;
  }
  
  
  public function delete($entity)
  {
    if ($entity->getUserId() == false)
    {
      throw new Exception("no user Id for entity???");
    }
    
    $mapper = $this->getShardedMapperByUserId($entity->getUserId());
    $mapper->delete($entity);
  }
                           
                           
  
    
                                                        
} 