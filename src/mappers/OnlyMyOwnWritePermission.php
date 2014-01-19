<?php 
namespace PhpSimpleOrm;

class OnlyMyOwnWritePermission
{
  //
  protected $mapper = false;

  function __construct()
  {
  }
  
  public function setReadRecorder($recorder)
  {
    $this->readRecorder = $recorder;
  }

  public function grantWritePermission($entity)
  {
    $this->readRecorder->recordRead($entity);  
  }

  public function isWritePermissionGranted($entity)
  {
    return ($this->readRecorder->didLoadEntity($entity));
  }   
        
}
      
    
class AlwaysWritePermission
{
  //
  protected $mapper = false;

  function __construct()
  {
  }
  
  public function isWritePermissionGranted($entity)
  {
    return true;
  }   

  public function grantWritePermission($entity)
  {
    return null;  
  }

  public function setReadRecorder($recorder)
  {
    $this->readRecorder = $recorder;
  }
        
}
      
      
