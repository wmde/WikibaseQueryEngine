<?php

namespace Wikibase\QueryEngine\Tests\Integration;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use PHPUnit_Framework_TestCase;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IntegrationStoreBuilder {

	const DB_NAME = 'qengine_tests';

	/**
	 * @param PHPUnit_Framework_TestCase $testCase
	 *
	 * @return SQLStoreWithDependencies
	 */
	public static function newStore( PHPUnit_Framework_TestCase $testCase ) {
		$builder = new self( $testCase );
		return $builder->buildStore();
	}

	private $testCase;

	private function __construct( PHPUnit_Framework_TestCase $testCase ) {
		$this->testCase = $testCase;
	}

	private function buildStore() {
		$handlers = new DataValueHandlers();
		$handlers->addMainSnakHandler( 'number', new NumberHandler( 'qr_snak_' ) );
		$handlers->addQualifierHandler( 'number', new NumberHandler( 'qr_qualifier_' ) );

		return new SQLStoreWithDependencies(
			new SQLStore(
				new StoreSchema(
					'qr_',
					$handlers
				),
				new StoreConfig( 'QueryEngine integration test store' )
			),
			$this->newConnection(),
			$this->newDataValueTypeLookupStub(),
			new BasicEntityIdParser()
		);
	}

	private function newDataValueTypeLookupStub() {
		$propertyDvTypeLookup = $this->testCase->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->testCase->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->testCase->returnValue( 'number' ) );

		return $propertyDvTypeLookup;
	}

	private function newConnection() {
		if ( !isset( $GLOBALS['db_type'] ) ) {
			return DriverManager::getConnection( array(
				'driver' => 'pdo_sqlite',
				'memory' => true,
			) );
		}

		$realDbParams = $this->getConnectionParams();
		$tmpDbParams = $this->getTempConnectionParams();

		$realConn = DriverManager::getConnection( $realDbParams );

		// Connect to tmpdb in order to drop and create the real test db.
		$tmpConn = DriverManager::getConnection( $tmpDbParams );

		$dbname = $realConn->getDatabase();
		$realConn->close();

		if ( in_array( $dbname, $tmpConn->getSchemaManager()->listDatabases() ) ) {
			$tmpConn->getSchemaManager()->dropDatabase( $dbname );
		}

		$tmpConn->getSchemaManager()->createDatabase( $dbname );

		$tmpConn->close();

		return DriverManager::getConnection( $realDbParams );
	}

	private function getConnectionParams() {
		return array(
			'driver' => $GLOBALS['db_type'],
			'user' => $GLOBALS['db_username'],
			'password' => $GLOBALS['db_password'],
			'host' => $GLOBALS['db_host'],
			'dbname' => $GLOBALS['db_name'],
			'port' => $GLOBALS['db_port']
		);
	}

	private function getTempConnectionParams() {
		$params = array(
			'driver' => $GLOBALS['tmpdb_type'],
			'user' => $GLOBALS['tmpdb_username'],
			'password' => $GLOBALS['tmpdb_password'],
			'host' => $GLOBALS['tmpdb_host'],
			'dbname' => null,
			'port' => $GLOBALS['tmpdb_port']
		);

		if (isset($GLOBALS['tmpdb_name'])) {
			$params['dbname'] = $GLOBALS['tmpdb_name'];
		}

		return $params;
	}

}