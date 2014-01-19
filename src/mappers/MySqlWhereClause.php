<?php 
namespace PhpSimpleOrm;


class MySqlWhereClause2013 extends \VisitableSpecification\AbstractCriteriaVisitor
{
  //
  protected $whereClause;
  protected $clauseParts = array();
  
  
  public function __construct($context)
  {
    $this->context = $context;
  }
  
  public function visitAndCriteria($andCriteria)
  {
    $firstClause = $this->getClauseForCriteria($andCriteria->getFirstCriteria());
    $secondClause = $this->getClauseForCriteria($andCriteria->getSecondCriteria());
                  
    $whereClause = " ( ".$firstClause. " AND ".$secondClause. " ) ";
    $this->setClauseForCriteria($andCriteria, $whereClause);
        
  }
  
  public function visitOrCriteria($orCriteria)
  {
          
    $firstClause = $this->getClauseForCriteria($orCriteria->getFirstCriteria());
    $secondClause = $this->getClauseForCriteria($orCriteria->getSecondCriteria());
                  
    $whereClause = " ( ".$firstClause. " OR ".$secondClause. " ) ";
    $this->setClauseForCriteria($orCriteria, $whereClause);
  }
  
  public function visitEqualCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." = '".addslashes($criteria->getValue())."' ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }

  
  public function visitNotEqualCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." != '".addslashes($criteria->getValue())."' ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }
 
  public function visitNotNullCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." IS NOT NULL ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }
 
  public function visitGreaterThanCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." > '".addslashes($criteria->getValue())."' ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }
   
  public function visitGreaterOrEqualCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." >= '".addslashes($criteria->getValue())."' ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }

  public function visitLessThanCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." < '".addslashes($criteria->getValue())."' ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }
  
  public function visitLessOrEqualCriteria($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ".$column." <= '".addslashes($criteria->getValue())."' ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }
         
  
  
  public function visitCriteriaBetween($criteria)
  {
    $column = $this->context->getResponsibleMapperForField($criteria->getField())->getPreparedColumnForField($criteria->getField());
    $whereClause = " ( ".$column." BETWEEN" .$criteria->getStartValue()." AND ".$criteria->getEndValue()." ) ";
    $this->setClauseForCriteria($criteria, $whereClause);
  }
  
  public function visitNotCriteria($criteria)
  {
    $whereClause = " NOT ( ".$this->getClauseForCriteria($criteria->getNestedCriteria()). " ) ";
    $this->setClauseForCriteria($criteria,$whereClause);
  }
  
  
  public function visitWithinDistanceCriteria($criteria)
  {
    throw new \ErrorException('Cannot use this engine for this query.');
  }
  
  
  public function getClauseForCriteria($criteria)
  {
    return $this->clauseParts[$criteria->getKey()];
  }

  protected function setClauseForCriteria($criteria,$clause)
  {
    $this->clauseParts[$criteria->getKey()] = $clause;
  }
    
} 





