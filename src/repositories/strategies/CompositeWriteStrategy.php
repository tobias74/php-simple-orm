<?php 
namespace PhpSimpleOrm;

class CompositeWriteStrategy
{
    
  protected $writeStrategies = array();  
     

  public function __construct($strategies=array())
  {
      $this->writeStrategies = $strategies;
  }  

  
  public function __call($name, $parameters)
  {
    foreach ($this->writeStrategies as $strategy)
    {
      call_user_func_array(array($strategy, $name), $parameters);
    }
  }
  
  /*               
  public function insert($entity)
  {
    foreach ($this->writeStrategies as $strategy)
    {
      $strategy->insert($entity);
    }
  }  

  public function update($entity)
  {
    foreach ($this->writeStrategies as $strategy)
    {
      $strategy->update($entity);
    }
  }  
  
  public function delete($entity)
  {
    foreach ($this->writeStrategies as $strategy)
    {
      $strategy->delete($entity);
    }
  }  
  */
    
}
