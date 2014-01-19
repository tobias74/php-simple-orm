<?php 
namespace PhpSimpleOrm;

class MySqlEngine2013 extends AbstractSqlEngine2013
{
  //
  
  public function getDbService()
  {
    return $this->dbShard->getMySqlService();
  }
  
  public function produceDbService()
  {
    return $this->dbShard->produceMySqlService();
  }

  protected function getWhereClause($spec,$mapper)
  {
    if ($spec->hasCriteria())
    {
      $sqlGenerator = new MySqlWhereClause2013($mapper);
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
  
  protected function getLimitClause($spec,$mapper)
  {
    return $spec->getLimitClauseForMySql($mapper);
  }
}
      
    

