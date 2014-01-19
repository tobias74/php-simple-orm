<?php 
namespace PhpSimpleOrm;

class AbstractRegularStrategy
{
    
  protected $mapper;  
  protected $mapperProvider;   


  public function getDebugName()
  {
    return "regular abstract shard";
  }

  public function setDatabaseShard($dbShard)
  {
    $this->databaseShard = $dbShard;
  }
  
  public function getDatabaseShard()
  {
    return $this->databaseShard;
  }
    
  public function setMapper($val)
  {
    $this->mapper = $val;
  }  
    
  public function setMapperProvider($provider)
  {
    $this->mapperProvider = $provider;
  }
  
  protected function produceMapper($shard)
  {
    return $this->mapperProvider->provide($shard);
  }
    
  
  protected function getMapper()
  {
    if (!isset($this->mapper))
    {
      $this->mapper = $this->produceMapper($this->databaseShard);
      
      // this should be set from outside, or should it or not or what?
      //$this->mapper->setProfiler($this->getProfiler());
    }
    return $this->mapper;
  }
  
  public function setProfiler($profiler)
  {
    $this->profiler = $profiler;
  }
    
      
}

