<?php 
namespace PhpSimpleOrm;

class DataProtectionWriteProxy extends AbstractDataProtectionProxy
{
  //
   
  
  public function setWritePermissionStrategy($strategy)
  {
    $this->writePermissionStrategy = $strategy;
  } 
   
  public function insert($entity)
  {
    // inserts should be ok. they are new.
    // and since we inserted this thing, we can put in in our list of know entites for write access.
    $this->writePermissionStrategy->grantWritePermission($entity);
    $this->getMapper()->insert($entity);
  }
    
  public function update($entity)
  {
    if ($this->writePermissionStrategy->isWritePermissionGranted($entity))
    {
      $this->getMapper()->update($entity);
    }
    else 
    {
      throw new \Exception('tried to write data into Database against policy');      
    }
  }
  
  public function delete($entity)
  {
    // deletes are ok as well. we only want to protect against wrong updates.
    $this->getMapper()->delete($entity);
  }
      
        
}
      
    
  
