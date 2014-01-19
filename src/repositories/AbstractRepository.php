<?php
namespace PhpSimpleOrm;

abstract class AbstractRepository
{
	protected $insertedEntity = false;
	
	public function __construct()
	{
		$args = func_get_args();
		if (count($args) != 0)
		{
			throw new ErrorException('Repositories do not take any construcor arguments');
		}
	}
	
	public function setProfiler($profiler)
	{
		$this->profiler = $profiler;
	}
	
	public function getProfiler()
	{
		return $this->profiler;
	}
    
    protected function getCriteriaMaker()
    {
        return new CriteriaMaker();
    }
    
	
    protected function getUniqueId()
    {
        $uid=uniqid();
        $uid.=rand(100000,999999);
        return $uid;
    }
    
    public function merge($entity)
    {
    	
      if ($entity->getId() === false)
      {
        $entity->injectValue('id',$this->getUniqueId());
        $this->insert($entity);
      }
      else
      {
        $this->update($entity);
      }
      
    }
    
    
    
    
	public function getSoleMatch($spec)
	{
		$items = $this->getBySpecification($spec);
		return $this->getFirstOnly($items);		
	}
	
    public function getFirstOnly($array)
    {
    	if (count($array) == 0)
    	{
    		throw new NoMatchException("Nahh did not find any in ".get_class($this));
    	}
    	elseif(count($array) > 1)
    	{
    		$item = array_shift($array);
    		throw new ErrorException("Nahh found too many(".(count($array)+1).")... with ".$item->getId(). " in ".get_class($this));
       	}
       	else
       	{
       		return array_shift($array);
       	}
    }

    
    
}
