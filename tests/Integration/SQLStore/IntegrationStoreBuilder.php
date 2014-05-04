<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

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

	private function newConnection() {
		$config = new Configuration();

		$connectionParams = array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		);

		return DriverManager::getConnection( $connectionParams, $config );
	}

	private function newDataValueTypeLookupStub() {
		$propertyDvTypeLookup = $this->testCase->getMock( 'Wikibase\QueryEngine\PropertyDataValueTypeLookup' );

		$propertyDvTypeLookup->expects( $this->testCase->any() )
			->method( 'getDataValueTypeForProperty' )
			->will( $this->testCase->returnValue( 'number' ) );

		return $propertyDvTypeLookup;
	}

}