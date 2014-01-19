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

  
  
  
  public function getClauseForOrderer($orderer)
  {
    return $this->clauseParts[$orderer->getKey()];
  }

  protected function setClauseForOrderer($orderer,$clause)
  {
    $this->clauseParts[$orderer->getKey()] = $clause;
  }
    
} 









class PostgreSqlOrderClause_ForUserQuery
{
  protected $orderClause;
  protected $clauseParts = array();
  protected $aliasClauses = array();
  protected $selectSorterClauses = array();
  
  
  public function __construct($mapper,$sqlEngine)
  {
    $this->context = $mapper;
    $this->sqlEngine = $sqlEngine;
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

      $aggregateFunction = array(
        'ASC' => 'min',
        'DESC' => 'max'
      );


      $aliasName = $this->context->getResponsibleMapperForField($singleOrderer->getField())->getColumnForField($singleOrderer->getField());

      if (in_array($singleOrderer->getField(), array('startDate','endDate')))
      {
        $aliasClause = $aggregateFunction[strtoupper($singleOrderer->getDirection())].'('.$this->context->getResponsibleMapperForField($singleOrderer->getField())->getPreparedColumnForField($singleOrderer->getField()).') AS '.$aliasName;

        $orderClause = ' '.$aliasName.' '.$singleOrderer->getDirection();

        $this->addAliasClause($aliasClause);
        

        // $selectSorterClause =  $this->sqlEngine->getSelectColumnsStringWithWhitelist($this->context->getStationMapper()->getMainMapper(), array( $singleOrderer->getField() ));

        // $this->addSelectSorterClause($selectSorterClause);
      }
      else
      {
    
        $column = $this->context->getResponsibleMapperForField($singleOrderer->getField())->getPreparedColumnForField($singleOrderer->getField());
        $orderClause = " ".$column." ".$singleOrderer->getDirection()." ";

      }


    $this->setClauseForOrderer($singleOrderer, $orderClause);

  }

  
  
  
  public function getClauseForOrderer($orderer)
  {
    return $this->clauseParts[$orderer->getKey()];
  }

  public function getAliasClause()
  {
    if (count($this->aliasClauses) > 0)
    {
      return " , ".implode("," , $this->aliasClauses);
    }
    else
    {
      return "";
    }


  }


  public function getSelectSorterClause()
  {
    if (count($this->selectSorterClauses) > 0)
    {
      return " , ".implode("," , $this->selectSorterClauses);
    }
    else
    {
      return "";
    }
  }

  public function getAdditionalSelectClauses()
  {
    $everything = array_merge($this->selectSorterClauses, $this->aliasClauses);

    if (count($everything) > 0)
    {
      return " , ".implode("," , $everything);
    }
    else
    {
      return "";
    }
  }




  protected function setClauseForOrderer($orderer,$clause)
  {
    $this->clauseParts[$orderer->getKey()] = $clause;
  }


  protected function addAliasClause($clause)
  {
    $this->aliasClauses[] = $clause;
  }

  protected function addSelectSorterClause($clause)
  {
    $this->selectSorterClauses[] = $clause;
  }
    
} 





