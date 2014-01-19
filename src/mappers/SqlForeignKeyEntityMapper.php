<?php 
namespace PhpSimpleOrm;

class SqlForeignKeyEntityMapper extends SqlEntityMapper
{
  protected $_foreignKeyColumn = 'abstract has to be set later';
  protected $_foreignKeyField = 'has to be set';
    
  
  public function setForeignKeyColumn($col)
  {
    $this->_foreignKeyColumn = $col;
  }
  
  public function getForeignKeyColumn()
  {
    return $this->_foreignKeyColumn;
  }

  public function setForeignKeyField($val)
  {
    $this->_foreignKeyField = $val;
  }
  
  public function getForeignKeyField()
  {
    return $this->_foreignKeyField;
  }
    
  
  public function deleteAllEntitiesByForeignKey($foreignKey)
  {
    $entities = $this->getAllEntitiesByForeignKey($foreignKey);
    foreach ($entities as $entity)
    {
      $this->delete($entity);
    }
  }
  
  public function getAllEntitiesByForeignKey($foreignKey)
  {
    if (!is_object($this->dbShard))
    {
      throw new ErrorException('coding error, no dbShard');
    }
    
    $dbService = $this->getDbService();

    $where = $this->_foreignKeyColumn."='".addslashes($foreignKey)."' ";
    $sql = $this->getSqlEngine()->assembleSelectQuery($where,$this);

    $resultSet = $dbService->query($sql, $this);
    return $this->loadAllFromResultSet($resultSet);
    
  }


  public function getSingleEntityByForeignKey($foreignKey)
  {
    return $this->getFirstOnly($this->getAllEntitiesByForeignKey($foreignKey));
  }


  
  public function getAllEntitiesByMultipleForeignKeys($foreignKeys)
  {
    if (!is_object($this->dbShard))
    {
      throw new ErrorException('coding error, no dbShard');
    }
    
    $dbService = $this->getDbService();
    
    $where = $this->getSqlEngine()->getInClause($this->_foreignKeyColumn, $foreignKeys);
    $sql = $this->getSqlEngine()->assembleSelectQuery($where,$this);
    $resultSet = $dbService->query($sql, $this);
    return $this->loadAllFromResultSet($resultSet);
    
  }


  
}
