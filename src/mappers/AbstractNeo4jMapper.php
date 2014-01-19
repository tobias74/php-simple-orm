<?php 
namespace PhpSimpleOrm;

abstract class AbstractNeo4jMapper
{
    function __construct($dbShard)
    {
        //        
        
        $this->client = null;
        $this->nodeIndex = null;  
        $this->dbShard = $dbShard;
            
        $this->client = $this->dbShard->getNeo4jService();    
        
        $this->declareDataMap();
        
    }
    
    public function getDebugName()
    {
      return "abstract neo4j mapper";
    }
    
    protected function getHashForEntity($entity)
    {
        $contentHash = array();
        
        foreach($this->dataMap->getFields() as $field)
        {
            $contentHash[$this->getColumnForField($field)] = $entity->getDryValue($field);
        }
        
        return $contentHash;
    }
    
    public function getColumnForField($field)
    {
        return $this->dataMap->getColumnForField($field);
    }
    
    public function getFieldForColumn($column)
    {
      return $this->dataMap->getFieldForColumn($column);
    }
            
    protected function writeNodePropertiesForEntity($entity,$node)
    {
      $hash = $this->getHashForEntity($entity);
      foreach ($hash as $field => $value)
      {
        $node->setProperty($field, $value);                
      }        
      $node->save();
    }
    
    
    protected function getNodeForEntity($entity)
    {
      return $this->getNodeForEntityId($entity->getId());
    }

    protected function getNodeForEntityId($entityId)
    {
      
      
      $node = $this->getIdIndex()->findOne('id',$entityId);
      
      
      if (!$node)
      {
        throw new NoMatchException();
      }
      
      return $node;
    }
              
    protected function getClient()
    {
      return $this->client;
    }
    
    protected function getIdIndex()
    {
      if (!$this->nodeIndex)
      {
        $this->nodeIndex = new \Everyman\Neo4j\Index\NodeIndex($this->getClient(), $this->getIndexName());
        
      }
      return $this->nodeIndex;
    }        

    protected function makeNode()
    {
       return $this->getClient()->makeNode();     
    }
            
    public function getBySpecification($spec)
    {
      $collectionName = $this->collectionName;
      
      $collection = $this->mongoDb->$collectionName;
      $resultSet = $collection->find($spec->getWhereMongoArray($this));
      
      return $this->loadAllFromResultSet($resultSet);
    }
                        

    protected function loadAllFromResultSet($resultSet)
    {
        $entities = array();
        foreach ($resultSet as $document)
        {
            $entities[] = $this->reinstantiateEntityFromDocument($document);
        }

        return $entities;
    }

    protected function reinstantiateEntityFromDocument($document)
    {
        $entity = $this->produceEmptyEntity();
        
        foreach ($document as $column => $value)
        {
          if ($column != '_id')
          {
            $entity->injectValue($this->getFieldForColumn($column), $value);
          }
        }
        return $entity;
    }
            
            
    public function delete($entity)
    {
        $node = $this->getNodeForEntity($entity);
        
        $relationships = $node->getRelationships();
        
        foreach ($relationships as $relation)
        {
          $relation->delete();
        }
        
        $node->delete();
    }
                                                    
    /*
    public function delete($zeitStation)
    {
        $this->getGroupAssocMapper()->removeAllAssociationsForStation($zeitStation->getId());
        $this->getFileMapper()->deleteAllEntitiesByForeignKey($zeitStation->getId());
        parent::delete($zeitStation);
    }
    
    
    
    protected function loadGroupAssociations($station)
    {
        $groupAssocMapper = $this->getGroupAssocMapper();
        $assignedGroupsIds = $groupAssocMapper->getAllAssociationsForStation($station->getId());
        $station->injectValue('assignedGroupsIds',$assignedGroupsIds);
    }
    
    protected function loadFiles($station)
    {
        $fileMapper = $this->getFileMapper();
        $files = $fileMapper->getAllEntitiesByForeignKey($station->getId());
        $station->injectValue('files', $files);
        $station->injectValue('previousFiles', $files);
    }
    
    protected function reinstantiateEntityFromRow($row)
    {
        $zeitStation = parent::reinstantiateEntityFromRow($row);
        
        $this->loadFilesForMultipleStations(array($entities));
        $this->loadGroupsForMultipleStations(array($entities));
        //$this->loadGroupAssociations($zeitStation);
        //$this->loadFiles($zeitStation);
        return $zeitStation;
    }
    
    protected function reinstantiateNakedEntityFromRow($row)
    {
        $zeitStation = parent::reinstantiateEntityFromRow($row);
        
        // leave out the expensive stuff, we wanna do that in bulk later.
        //$this->loadGroupAssociations($zeitStation);
        //$this->loadFiles($zeitStation);
        return $zeitStation;
    }
    
    // we overwrite loadAllFromResultSet, for performance reasons
    public function loadAllFromResultSet($resultSet)
    {
        $entities = array();
        while ($row = $resultSet->fetchObject())
        {
            $entities[] = $this->reinstantiateNakedEntityFromRow($row);
        }
        
        // we have naked entities at this point, let load the files in bulk now.
        $this->loadFilesForMultipleStations($entities);
        $this->loadGroupsForMultipleStations($entities);
        return $entities;
    }

    protected function loadFilesForMultipleStations($stations)
    {
        $fileMapper = $this->getFileMapper();
        $groupAssocMapper = $this->getGroupAssocMapper();
        
        $stationIds = array();
        $filesByStationId = array();
        
        foreach($stations as $station)
        {
            $stationIds[] = $station->getId();
            $filesByStationId[$station->getId()] = array();
        }
        
        $files = $fileMapper->getAllEntitiesByMultipleForeignKeys($stationIds);
            
        
        foreach($files as $file)
        {
            $stationId = $file->getStationId();
            $filesByStationId[$stationId][] = $file;
        }
        
        foreach($stations as $station)
        {
            $station->injectValue('files', $filesByStationId[$station->getId()]);
            $station->injectValue('previousFiles', $filesByStationId[$station->getId()]);
        }
        
    }
    
    protected function loadGroupsForMultipleStations($stations)
    {
        $groupAssocMapper = $this->getGroupAssocMapper();
        
        $stationIds = array();
        foreach($stations as $station)
        {
            $stationIds[] = $station->getId();
        }
        
        $groupAssociationsByStationId = $groupAssocMapper->getAssociationsForMultipleStations($stationIds);
        
        
        foreach($stations as $station)
        {
            $station->injectValue('assignedGroupsIds', $groupAssociationsByStationId[$station->getId()]);
        }
        
    }
        
    
    
    */
    
    
    
        
}


