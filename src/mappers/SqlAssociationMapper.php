<?php 
namespace PhpSimpleOrm;

class SqlAssociationMapper extends SqlTableMapper
{
  
  
  public function insert($assoc)
  {
    $this->insertQuery($assoc);
  }
    
  public function delete($assoc)
  {
    $where = $this->getWhereClause($assoc);
    $this->deleteQuery($where);
  }
    
  public function select($assoc)
  {
    $where = $this->getWhereClause($assoc);
    return $this->selectQuery($where);
  }

    
  protected function getWhereClause($assoc)
  {
    $where = $this->getSqlEngine()->getConjunctionClause($assoc);
    return $where;
  }
  
    
    
    
  public function __call($name, $arguments)
  {
    if ( substr($name,0,9) == "associate" )
    {
      $calledCombination = substr($name,9);
      $fields = $this->dataMap->getFields();
      $aWithB = ucfirst($fields[0])."With".ucfirst($fields[1]);
      $bWithA = ucfirst($fields[1])."With".ucfirst($fields[0]);
      
      $columns = $this->dataMap->getColumns();
      
      if ($calledCombination === $aWithB)
      {
        $assoc = array(
          $this->getColumnForField($fields[0]) => $arguments[0],
          $this->getColumnForField($fields[1]) => $arguments[1]
        );
        
        $this->insert($assoc);
      }
      elseif ($calledCombination === $bWithA)
      {
        $assoc = array(
          $this->getColumnForField($fields[1]) => $arguments[0],
          $this->getColumnForField($fields[0]) => $arguments[1]
        );
        
        $this->insert($assoc);
      }
      else
      {
        throw new ErrorException('Application Error, please code correctly.');
      }
      
    }
    elseif ( substr($name,0,10) == "dissociate" )
    {
      $calledCombination = substr($name,10);
      $fields = $this->dataMap->getFields();
      $aWithB = ucfirst($fields[0])."From".ucfirst($fields[1]);
      $bWithA = ucfirst($fields[1])."From".ucfirst($fields[0]);
      
      $columns = $this->dataMap->getColumns();
      
      if ($calledCombination === $aWithB)
      {
        $assoc = array(
          $this->getPreparedColumnForField($fields[0]) => $arguments[0],
          $this->getPreparedColumnForField($fields[1]) => $arguments[1]
        );
        
        $this->delete($assoc);
      }
      elseif ($calledCombination === $bWithA)
      {
        $assoc = array(
          $this->getPreparedColumnForField($fields[1]) => $arguments[0],
          $this->getPreparedColumnForField($fields[0]) => $arguments[1]
        );
        
        $this->delete($assoc);
      }
      else
      {
        throw new ErrorException('Application Error, please code correctly.');
      }
    }
    elseif ( substr($name,0,21) == "getAllAssociationsFor" )
    {
      $calledField = lcfirst(substr($name,21));
      $fields = $this->dataMap->getFields();
      if ($fields[0] === $calledField)
      {
        $wantedField = $fields[1];
      }
      elseif ($fields[1] === $calledField)
      {
        $wantedField = $fields[0];
      }
      else
      {
        throw new ErrorException('bad codding here...'.$wantedField);
      }

      $assoc = array(
        $this->getPreparedColumnForField(lcfirst($calledField)) => $arguments[0]
      );
      
      $dbResult = $this->select($assoc);
      
      $wantedColumn = $this->getColumnForField($wantedField);
      
      $itemIds=array();
      while ($row = $dbResult->fetchObject())
      {
        array_push($itemIds, $row->$wantedColumn);
      }
      
      return $itemIds;
      
    }
    elseif ( substr($name,0,21) == "getSoleAssociationFor" )
    {
      $calledField = substr($name,21);
      $methodName= "getAllAssociationsFor".$calledField;
      $itemIds = $this->$methodName($arguments[0]);
      if (count($itemIds) > 1)
      {
        throw new ErrorException('bad database here, we have to many assiciations for this: '.$calledField);
      }
      elseif (count($itemIds) === 1)
      {
        return $itemIds[0];
      }
      else 
      {
        throw new NoMatchException('did not find any associations for '.$calledField);
        // what here?
      }
      
    }
    elseif ( substr($name,0,26) == "getAssociationsForMultiple" )
    {
      $calledField = lcfirst(substr($name,26));
      $calledField = substr($calledField,0,strlen($calledField)-1);
      
      $fields = $this->dataMap->getFields();
      if ($fields[0] === $calledField)
      {
        $wantedField = $fields[1];
      }
      elseif ($fields[1] === $calledField)
      {
        $wantedField = $fields[0];
      }
      else
      {
        throw new ErrorException('bad codding here... '.$calledField);
      }
      
      $itemIds = $arguments[0];
      if (count($itemIds) == 0)
      {
        return array();
      }
      
      $associationsByItemId = array();
      
      $pairs=array();
      foreach($itemIds as $itemId)
      {
        $pairs[] = $this->getPreparedColumnForField(lcfirst($calledField))."='".addslashes($itemId)."'";
        $associationsByItemId[$itemId] = array();
      }

      $where = implode(" OR ", $pairs);
      
      $dbResult = $this->selectQuery($where);
      
      $wantedColumn = $this->getColumnForField($wantedField);
      $calledColumn = $this->getColumnForField($calledField);
      
      
      while ($row = $dbResult->fetchObject())
      {
        if (!isset($associationsByItemId[$row->$calledColumn]))
        {
          throw new ErrorException('this id should be here: '.$row->$calledColumn.' for '.$calledColumn);
        }
        
        array_push($associationsByItemId[$row->$calledColumn], $row->$wantedColumn);
      }

      return $associationsByItemId;
      
    }
    elseif ( substr($name,0,24) == "removeAllAssociationsFor" )
    {
      $calledField = lcfirst(substr($name,24));
      $fields = $this->dataMap->getFields();
      if ($fields[0] === $calledField)
      {
        $wantedField = $fields[1];
      }
      elseif ($fields[1] === $calledField)
      {
        $wantedField = $fields[0];
      }
      else
      {
        throw new ErrorException("bad codding here... with $calledField");
      }

      $assoc = array(
        $this->getPreparedColumnForField(lcfirst($calledField)) => $arguments[0]
      );
      
      $dbResult = $this->delete($assoc);
      
    }
    else
    {
      throw new \Exception("call to undefined method ".$name);
    }
    
  }
  
    
}

