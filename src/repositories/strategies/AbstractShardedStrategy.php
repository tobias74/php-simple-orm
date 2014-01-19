<?php 
namespace PhpSimpleOrm;



class AbstractShardedStrategy
{
  private $mappers = array();
    
  public function __construct()
  {
    
  }  
  
  public function getDebugName()
  {
    return "sharded abstract shard";
  }
    
  public function setMapperProvider($provider)
  {
    $this->mapperProvider = $provider;
  }

  protected function produceMapper($shard)
  {
    return $this->mapperProvider->provide($shard);
  }
      
  public function setShardingService($service)
  {
    $this->shardingService = $service;
  }
  
  public function getShardingService()
  {
      return $this->shardingService;    
  }

  
  protected function getShardByUserId($userId)
  {
    return $this->shardingService->getShardByUserId($userId);
  }

  protected function getAllShards()
  {
    return $this->shardingService->getAllShards();  
  }
  
  protected function getMapper($shard)
  {
    // we do some optimizing caching here, we store the mappers for each shard by shardId :-)
    
    if (!isset($this->mappers[$shard->getId()]))
    {
      $this->mappers[$shard->getId()] = $this->produceMapper($shard);
    }
    
    return $this->mappers[$shard->getId()];
  }
  
  protected function getShardedMapperByUserId($userId)
  {
      $shard = $this->getShardByUserId($userId);
      $mapper = $this->getMapper($shard);
      return $mapper;
    }

  protected function getShardedMapperByShardId($shardId)
  {
    if ($shardId == false)
    {
      die('application error in '.get_class($this));  
    }
    
    try
    {
        $shard = $this->shardingService->getShardById($shardId);
    }
    catch (ZeitfadenNoMatchException $e)
    {
      throw new ErrorException('we did not find a shard for -'.$shardId.'- in '.get_class($this));
    }
      
      return $this->getMapper($shard);
  }
      
  
  public function setProfiler($profiler)
  {
    $this->profiler = $profiler;
  }
    
}

