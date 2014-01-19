<?php 
namespace PhpSimpleOrm;

abstract class AbstractRiakMapper
{
    protected $tableName;
  
    function __construct($dbShard)
    {
        //        
          
        $this->dbShard = $dbShard;
            
        $this->client = $this->dbShard->getRiakClient();    
        
        $this->declareBucketName();
        
    }
    
    protected function getBucket()
    {
      return $this->client->bucket($this->getBucketName());
    }
    
    public function getDebugName()
    {
      return "abstract riak mapper";
    }
    
    protected function getBucketName()
    {
      return $this->dbShard->getRiakBucketPrefix().$this->bucketName;
    }

    public function setProfiler($profiler)
    {
      $this->profiler = $profiler;
    }
            
    public function getProfiler()
    {
      return $this->profiler;
    }        

    
    public function getRiakForEntityId($id)
    {
      return $this->getBucket()->get($id);
    }        
                        

    
    
    
        
}


