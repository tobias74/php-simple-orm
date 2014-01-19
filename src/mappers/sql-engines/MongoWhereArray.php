<?php 
namespace PhpSimpleOrm;


class MongoWhereArray extends \VisitableSpecification\AbstractCriteriaVisitor
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
    $firstArray = $this->getArrayForCriteria($andCriteria->getFirstCriteria());
    $secondArray = $this->getArrayForCriteria($andCriteria->getSecondCriteria());
   
    $whereArray = array('$and' => array(
      $firstArray,
      $secondArray
    ));
      
                  
    $this->setArrayForCriteria($andCriteria, $whereArray);
        
  }
  
  public function visitOrCriteria($orCriteria)
  {
    //
    $firstArray = $this->getArrayForCriteria($orCriteria->getFirstCriteria());
    $secondArray = $this->getArrayForCriteria($orCriteria->getSecondCriteria());
   
    $whereArray = array('$or' => array(
      $firstArray,
      $secondArray
    ));
      
                  
    $this->setArrayForCriteria($orCriteria, $whereArray);
              
  }

  
  public function visitEqualCriteria($criteria)
  {
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => $criteria->getValue());
    
    $this->setArrayForCriteria($criteria, $comp);
  }
  
      
  public function visitGreaterThanCriteria($criteria)
  {
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => array(
      '$gt' => $criteria->getValue()
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
  }


  public function visitGreaterOrEqualCriteria($criteria)
  {
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => array(
      '$gte' => $criteria->getValue()
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
  }

  
  public function visitLessThanCriteria($criteria)
  {
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => array(
      '$lt' => $criteria->getValue()
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
  }
    
    
  public function visitLessOrEqualCriteria($criteria)
  {
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => array(
      '$lte' => $criteria->getValue()
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
  }
        
    
  public function visitNotEqualCriteria($criteria)
  {
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => array(
      '$ne' => $criteria->getValue()
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
  }
    
        
    /*
  public function visitAssociationCriteria($criteria)
  {
    // not yet needed. mongo is cool enough.
    //    
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array(
      $column => array('$in' => array(
          $criteria->getValue()
        )
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
    
  }
  */
    
  public function visitCriteriaBetween($criteria)
  {
    //
    $mapper = $this->context->getResponsibleMapperForField($criteria->getField());
    $column = $mapper->getColumnForField($criteria->getField());

    $comp = array($column => array(
      '$gt' => $criteria->getStartValue(),
      '$lt' => $criteria->getEndValue()
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
        
  }
  
  public function visitNotCriteria($criteria)
  {
    //
    $comp = array('$not' => array(
      $this->getArrayForCriteria($criteria->getNestedCriteria()),
    ));
        
    $this->setArrayForCriteria($criteria, $comp);
    
    
  }
  
  
  public function visitWithinDistanceCriteria($criteria)
  {
    //
    $mapper = $this->context;
    $column = $mapper->getColumnForField($criteria->getGeometryField());

    $comp = array(
      $column => array(
        '$geoWithin' => array(
          '$centerSphere' => array(
            array(floatval($criteria->getLongitude()), floatval($criteria->getLatitude())),
            floatval($criteria->getMaximumDistance()/3959)
          )
        )
      )
    );
    
    $this->setArrayForCriteria($criteria, $comp);
          
  }
  
  
  public function getArrayForCriteria($criteria)
  {
    return $this->clauseParts[$criteria->getKey()];
  }

  protected function setArrayForCriteria($criteria,$clause)
  {
    $this->clauseParts[$criteria->getKey()] = $clause;
  }
    
} 





