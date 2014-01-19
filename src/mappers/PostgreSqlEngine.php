<?php 
namespace PhpSimpleOrm;

class PostgreSqlEngine2013 extends AbstractSqlEngine2013
{
  //
  
  public function getDbService()
  {
    return $this->dbShard->getPostgreSqlService();
  }
  
  public function produceDbService()
  {
    return $this->dbShard->producePostgreSqlService();
  }

  protected function getWhereClause($spec,$mapper)
  {
    if ($spec->hasCriteria())
    {
      $sqlGenerator = new PostgreSqlWhereClause2013($mapper);
      $spec->getCriteria()->acceptVisitor($sqlGenerator);
      $whereClause = " WHERE ".$sqlGenerator->getClauseForCriteria($spec->getCriteria());
            
    }
    else 
    {
      $whereClause=" ";
    }
        
    //error_log('this is what we got: '.$whereClause);
    return $whereClause;
    //return $spec->getWhereClause($mapper);
  }


  protected function getOrderClause($spec,$mapper)
  {
    if ($spec->hasOrderer())
    {
      $sqlGenerator = new PostgreSqlOrderClause($mapper);
      $spec->getOrderer()->acceptVisitor($sqlGenerator);
      $orderClause = " ORDER BY ".$sqlGenerator->getClauseForOrderer($spec->getOrderer());
            
    }
    else 
    {
      $orderClause=" ";
    }
        
    //error_log('this is what we got: '.$whereClause);
    return $orderClause;
    //return $spec->getWhereClause($mapper);
  }

  
  protected function getLimitClause($spec,$mapper)
  {
    return $spec->getLimitClauseForPostgreSql($mapper);
  }
}
      
    

