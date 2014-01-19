<?php 
namespace PhpSimpleOrm;

abstract class DomainObject
{

	protected $id = false;
  protected $_shardId;
	
	public function __construct()
	{
		
	}
	
  public function setShardId($val)
  {
    $this->_shardId = $val; 
  }
  
  public function getShardId()
  {
    return $this->_shardId;
  }

	public function getId()
	{
		return $this->id;
	}
	
	public function injectValue($field,$value)
	{
		$this->$field = $value;
	}
	
	public function getDryValue($field)
	{
		return $this->$field;
	}
	
	public function getDebugName()
	{
	  return print_r($this->declareSynthesizedProperties(), true);
	}
	
	protected function declareSynthesizedProperties()
	{
		return array();
	}
	
	public function __call($name, $arguments)
	{
		if ( substr($name,0,3) == "set" )
		{
			$property = $this->lcfirst(substr($name,3));
			if (array_search($property, $this->declareSynthesizedProperties()) === false)
			{
				throw new \Exception("Called Setter or Getter on non existing roperty. ".$property );
			}
			$this->$property = array_shift($arguments);
		}
		elseif ( substr($name,0,3) == "get" )
		{
			$property = $this->lcfirst(substr($name,3));
			//if (array_search($property, $this->declareSynthesizedProperties()) === false)
			//{
			//	throw new \Exception("Called Setter or Getter on non existing roperty. ".$property);
			//}
			return $this->$property;
		}
		else
		{
			throw new \Exception("call to undefined method ".$name);
		}
		
	}
	

	protected function lcfirst( $str ) 
    { 
    	return (string)(strtolower(substr($str,0,1)).substr($str,1));
    } 

    
    protected function isSameInstance($objA, $objB ) 
    { 
    	if ($objA === $objB)
    	{
    		return 0;
    	}
    	else
    	{
    		return 1;
    	}
    } 
    
}
