<?php 
namespace PhpSimpleOrm;


class ShardedReadStrategy extends AbstractShardedStrategy
{
  //
    public function getById($entityId, $userId)
    {
        $mapper = $this->getShardedMapperByUserId($userId);
        // ok, pre-selecting the mapper and going straight into it is optimization.
        // we could just use our own gtBySpecification and let the search b done on all shards.
        
        return $mapper->getSoleMatch(new \BrokenPottery\Specification($this->criteria->hasId($entityId) -> logicalAnd($this->criteria->belongsToUser($userId))));
    }
    
    public function existsId($entityId, $userId)
    {
        $mapper = $this->getShardedMapperByUserId($userId);
        $criteria = $this->criteria->hasId($entityId) -> logicalAnd($this->criteria->belongsToUser($userId));
        return $mapper->countByCriteria($criteria); 
    }

    public function release($entity)
    {
      $mapper = $this->getShardedMapperByUserId($entity->getUserId());
      $mapper->release($station);  
    }
    
    
    public function getBySpecification($spec)
    {
      // we must bow give the limiter to the mapper, because limitting
      // has to be done after all result-sets have been collected
      $oldLimiter = $spec->getLimiter();
      if (is_object($oldLimiter))
        {
          $maximumPossibleDepth = $oldLimiter->getOffset() + $oldLimiter->getLength();
        }
        else 
        {
            $maximumPossibleDepth = 1000;
        }
    $unLimiter = new Limiter(0, $maximumPossibleDepth);

    //echo "We havemaximumDepth: ".$maximumPossibleDepth;
    
    $newSpec = clone $spec;
    $newSpec->setLimiter($unLimiter);   
      
      $shardList = $this->getAllShards();
    
    $parallelLoader = new ParallelLoader();
          
      foreach ($shardList as $shard)
      {
        $mapper = $this->getMapper($shard);
      $parallelLoader->addMapperWithSpecification($mapper, $newSpec);
        }
      
        $entities = $parallelLoader->runMappers();
    
      if (is_object($newSpec->getOrderer()))
      {
        $entities = $this->orderEntities($entities,$newSpec->getOrderer());
      }
      
      if (is_object($oldLimiter))
      {
        $entities = $this->limitEntities($entities,$oldLimiter);
      }
      
      
    return $entities;     
      
    }

    
    protected function limitEntities($entities, $limiter)
    {
      $entities = array_slice($entities, $limiter->getOffset(), $limiter->getLength());
      return $entities; 
    }
        
        
    protected function orderEntities($entities, $orderer)
    {
      return $entities;
    }
    
            
    protected function areLoadersActive($loaders)
    {
      foreach($loaders as $loader)
      {
        if ($loader->isLoading())
        {
          return true;
        }
      } 
      
      return false;
    }
    
}

