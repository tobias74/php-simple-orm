<?php 
namespace PhpSimpleOrm;

class DataProtectionReadRecorder
{
  //
  protected $loadedEntities = array();
  
  public function reset()
  {
    $this->loadedEntities = array();
  }
     
  public function didLoadEntity($entity)
  {
    return (array_search($entity->getId(), $this->loadedEntities) !== false);
  }
   
  public function recordRead($entity)
  {
    $this->loadedEntities[] = $entity->getId();
    $this->loadedEntities = array_unique($this->loadedEntities);
  } 
        
  public function unRecord($entity)
  {
    $pos = array_search($entity->getId(), $this->loadedEntities);
    
    if ($pos === false)
    {
      return;
    }
    else 
    {
      unset($this->loadedEntities[$pos]);
      $this->loadedEntities = array_merge($this->loadedEntities);
      $this->loadedEntities = array_unique($this->loadedEntities);
            
    }
    
        
  }      
}
      
    
  
