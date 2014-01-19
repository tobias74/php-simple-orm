<?php 
namespace PhpSimpleOrm;

class AbstractDataProtectionProxy // implements the upwards mapper-interface. this acts like a mapper from the repository-point of view
{
  //
  protected $mapper = false;
  protected $dbShard;

  function __construct($dbShard)
  {
    $this->dbShard = $dbShard;
  }
  
  // the next lines would be too dangerous and dirty.
  /*
  public function __call($methodName, $args) 
  {
    return call_user_func_array(array($this->mapper, $methodName), $args);
  }
 */
      
  public function setMapperProvider($mapperProvider)
  {
    $this->mapperProvider = $mapperProvider;
  }
  
  protected function getMapper()
  {
    if (!$this->mapper)
    {
      $this->mapper = $this->mapperProvider->provide($this->dbShard);
    }
    return $this->mapper;
  }
   
        
}
      
    
  
