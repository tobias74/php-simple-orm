<?php

 
require_once(dirname(__FILE__).'/DomainObject.php');
require_once(dirname(__FILE__).'/Exceptions.php');

require_once(dirname(__FILE__).'/database/db-connector/MySqlConnector.php');
require_once(dirname(__FILE__).'/database/db-connector/PostgreSqlConnector.php');
require_once(dirname(__FILE__).'/database/DbConfig.php');
require_once(dirname(__FILE__).'/database/DbResult.php');
require_once(dirname(__FILE__).'/database/DatabaseShard.php');
require_once(dirname(__FILE__).'/database/DbService.php');



require_once(dirname(__FILE__).'/mappers/DataMap.php');
require_once(dirname(__FILE__).'/mappers/sql-engines/MongoWhereArray.php');

require_once(dirname(__FILE__).'/mappers/AbstractDataProtectionProxy.php');
require_once(dirname(__FILE__).'/mappers/DataProtectionReadRecorder.php');
require_once(dirname(__FILE__).'/mappers/DataProtectionWriteProxy.php');
require_once(dirname(__FILE__).'/mappers/OnlyMyOwnWritePermission.php');

require_once(dirname(__FILE__).'/mappers/AbstractMongoMapper.php');
require_once(dirname(__FILE__).'/mappers/AbstractNeo4jMapper.php');
require_once(dirname(__FILE__).'/mappers/AbstractHbaseMapper.php');
require_once(dirname(__FILE__).'/mappers/AbstractRiakMapper.php');

require_once(dirname(__FILE__).'/mappers/SqlTableMapper.php');
require_once(dirname(__FILE__).'/mappers/SqlAssociationMapper.php');
require_once(dirname(__FILE__).'/mappers/SqlEntityMapper.php');
require_once(dirname(__FILE__).'/mappers/AbstractSqlEngine.php');
require_once(dirname(__FILE__).'/mappers/MySqlEngine.php');
require_once(dirname(__FILE__).'/mappers/PostgreSqlEngine.php');
require_once(dirname(__FILE__).'/mappers/PostgreSqlWhereClause.php');
require_once(dirname(__FILE__).'/mappers/PostgreSqlOrderClause.php');
require_once(dirname(__FILE__).'/mappers/MySqlWhereClause.php');
require_once(dirname(__FILE__).'/mappers/SqlForeignKeyEntityMapper.php');



require_once(dirname(__FILE__).'/repositories/AbstractRepository.php');
require_once(dirname(__FILE__).'/repositories/AbstractStrategizedRepository.php');
require_once(dirname(__FILE__).'/repositories/strategies/AbstractRegularStrategy.php');
require_once(dirname(__FILE__).'/repositories/strategies/RegularReadStrategy.php');
require_once(dirname(__FILE__).'/repositories/strategies/RegularWriteStrategy.php');
require_once(dirname(__FILE__).'/repositories/strategies/CompositeWriteStrategy.php');







