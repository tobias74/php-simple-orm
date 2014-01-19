<?php 
namespace PhpSimpleOrm;



class RegularWriteStrategy extends AbstractRegularStrategy
{
  public function __call($name, $parameters)
  {
    $mapper = $this->getMapper();
    return call_user_func_array(array($mapper, $name), $parameters);
  }
     
  /* 
    
  public function update($entity)
  {
    $this->getMapper($this->databaseShard)->update($entity);        
    return $entity;
  }

  public function insert($entity)
  {
    $this->getMapper($this->databaseShard)->insert($entity);        
    return $entity;
  }

  public function delete($entity)
  {
    $this->getMapper($this->databaseShard)->delete($entity);
  }
  */
}

