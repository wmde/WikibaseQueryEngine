<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\DataValue;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\SQLStore\SQLStoreWithDependencies;
use Wikibase\QueryEngine\Tests\Integration\IntegrationStoreBuilder;

/**
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
abstract class DataValueHandlerIntegrationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SQLStoreWithDependencies
	 */
	private $store;

	/**
	 * @var int
	 */
	private $guidCounter = 0;

	public function setUp() {
		$this->store = IntegrationStoreBuilder::newStore( $this );

		$this->store->newInstaller()->install();

		$this->insertItems();
	}

	public function tearDown() {
		if ( isset( $this->store ) ) {
			$this->store->newUninstaller()->uninstall();
		}
	}

	private function getPropertyId() {
		return new PropertyId( 'P7701' );
	}

	/**
	 * @return string
	 */
	abstract public function getDataValueType();

	/**
	 * @return Item[]
	 */
	abstract protected function insertItems();

	/**
	 * @param string $idString
	 * @param DataValue|null $dataValue
	 */
	protected function insertItem( $idString, DataValue $dataValue = null ) {
		$item = Item::newEmpty();
		$item->setId( new ItemId( $idString ) );

		if ( $dataValue !== null ) {
			$propertyId = $this->getPropertyId();
			$mainSnak = new PropertyValueSnak( $propertyId, $dataValue );
			$statement = new Statement( $mainSnak );

			$statement->setGuid( 'statement' . $this->guidCounter );
			$this->guidCounter++;

			$item->addClaim( $statement );
		}

		$this->store->newWriter()->insertEntity( $item );
	}

	/**
	 * @dataProvider queryProvider
	 * @param DataValue $dataValue
	 * @param string[] $expectedIdStrings
	 */
	public function testQuery( DataValue $dataValue, array $expectedIdStrings ) {
		$someProperty = new SomeProperty(
			new EntityIdValue( $this->getPropertyId() ),
			new ValueDescription( $dataValue )
		);

		$queryEngine = $this->store->newQueryEngine();
		$options = new QueryOptions( 100, 0 );
		$entityIds = $queryEngine->getMatchingEntities( $someProperty, $options );

		$idStrings = $this->getIdStrings( $entityIds );
		$this->assertSameElements( $expectedIdStrings, $idStrings );
	}

	/**
	 * @return array[]
	 */
	abstract public function queryProvider();

	/**
	 * @param EntityId[] $entityIds
	 *
	 * @return string[]
	 */
	private function getIdStrings( array $entityIds ) {
		$idStrings = array();

		foreach ( $entityIds as $entityId ) {
			$idStrings[] = $entityId->getSerialization();
		}

		return $idStrings;
	}

	/**
	 * @param string[] $expected
	 * @param string[] $actual
	 */
	private function assertSameElements( array $expected, array $actual ) {
		$this->assertCount( count( $expected ), $actual );

		sort( $expected );
		sort( $actual );

		$this->assertSame( $expected, $actual );
	}

}
