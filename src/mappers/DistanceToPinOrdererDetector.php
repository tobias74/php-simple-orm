<?php
namespace PhpSimpleOrm;


class DistanceToPinOrdererDetector
{

  public function __construct()
  {
  	$this->detectedOrderer = null;
  	$this->hasDistanceToPinOrderer = false;
  }
  
  public function visitChainedOrderer($chainedOrderer)
  {
  }
  
  
  
  public function visitSingleOrderer($singleOrderer)
  {
  }

  
  public function visitDistanceToPinOrderer($distanceToPinOrderer)
  {
  	$this->hasDistanceToPinOrderer = true;
	  $this->detectedOrderer = $distanceToPinOrderer;
  }
  
  public function getDistanceToPinOrderer()
  {
  	return $this->detectedOrderer;
  }
  
  public function hasDistanceToPinOrderer()
  {
  	return $this->hasDistanceToPinOrderer;
  }
}

  
