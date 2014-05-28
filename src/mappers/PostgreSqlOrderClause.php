<?php 
namespace PhpSimpleOrm;


class PostgreSqlOrderClause
{
  protected $orderClause;
  protected $clauseParts = array();
  
  
  public function __construct($context)
  {
    $this->context = $context;
  }
  
  public function visitChainedOrderer($chainedOrderer)
  {
    $firstClause = $this->getClauseForOrderer($chainedOrderer->getFirstOrderer());
    $secondClause = $this->getClauseForOrderer($chainedOrderer->getSecondOrderer());
                  
    $orderClause = "  ".$firstClause. " , ".$secondClause. "  ";
    $this->setClauseForOrderer($chainedOrderer, $orderClause);
        
  }
  
  
  
  public function visitSingleOrderer($singleOrderer)
  {
    $column = $this->context->getResponsibleMapperForField($singleOrderer->getField())->getPreparedColumnForField($singleOrderer->getField());

    $orderClause = " ".$column." ".$singleOrderer->getDirection()." ";
    $this->setClauseForOrderer($singleOrderer, $orderClause);
  }

  
  public function visitDistanceToPinOrderer($distanceToPinOrderer)
  {
    $column = $this->context->getResponsibleMapperForField($distanceToPinOrderer->getField())->getPreparedColumnForField($distanceToPinOrderer->getField());

	$orderClause = "ST_Distance(".$column." , ST_MakePoint(".$distanceToPinOrderer->getLongitude().",".$distanceToPinOrderer->getLatitude().")::geography)";

    if ($distanceToPinOrderer->getDirection() == 'desc')
	{
	    $orderClause .= ' DESC ';
	} 
	else 
	{
	    $orderClause .= ' ASC ';
	}
	
    $this->setClauseForOrderer($distanceToPinOrderer, $orderClause);
  }
  
  public function getClauseForOrderer($orderer)
  {
    return $this->clauseParts[$orderer->getKey()];
  }

  protected function setClauseForOrderer($orderer,$clause)
  {
    $this->clauseParts[$orderer->getKey()] = $clause;
  }
    
} 



class UnpreparedPostgreSqlOrderClause extends PostgreSqlOrderClause
{
  
  public function visitSingleOrderer($singleOrderer)
  {
    $column = $this->context->getResponsibleMapperForField($singleOrderer->getField())->getColumnForField($singleOrderer->getField());

    $orderClause = " ".$column." ".$singleOrderer->getDirection()." ";
    $this->setClauseForOrderer($singleOrderer, $orderClause);
  }
  
}









