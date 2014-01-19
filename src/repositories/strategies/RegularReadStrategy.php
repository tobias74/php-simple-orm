<?php 
namespace PhpSimpleOrm;


class RegularReadStrategy extends AbstractRegularStrategy
{
    
  public function __call($name, $parameters)
  {
    $mapper = $this->getMapper();
    return call_user_func_array(array($mapper, $name), $parameters);
  }
  
/*      
    public function getBySpecification($spec)
    {
      $entities = $this->getMapper($this->databaseShard)->getBySpecification($spec);
    return $entities;
    }

    public function countByCriteria($criteria)
    {
      $count = $this->getMapper($this->databaseShard)->countByCriteria($criteria);
      return $count;
    }
  
    public function release($station)
    {
      $this->getMapper($this->databaseShard)->release($station);  
    }
*/    
  
}
