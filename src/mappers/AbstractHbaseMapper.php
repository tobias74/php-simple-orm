<?php 
namespace PhpSimpleOrm;

abstract class AbstractHbaseMapper
{
    protected $tableName;
  
    function __construct($dbShard)
    {
        //        
          
        $this->dbShard = $dbShard;
            
        $this->client = $this->dbShard->getHbaseClient();    
        
        $this->declareTableName();
        
    }
    
    public function getDebugName()
    {
      return "abstract hbase mapper";
    }
    
    protected function getTableName()
    {
      return $this->dbShard->getHbaseTablePrefix().$this->tableName;
    }

    public function setProfiler($profiler)
    {
      $this->profiler = $profiler;
    }
            
    public function getProfiler()
    {
      return $this->profiler;
    }        

            
                        

    
    
    
        
}


