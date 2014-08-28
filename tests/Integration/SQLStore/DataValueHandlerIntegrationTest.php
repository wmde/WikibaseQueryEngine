<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\DataValue;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
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

	/**
	 * @return string
	 */
	abstract public function getDataValueType();

	abstract protected function insertItems();

	/**
	 * @return array[]
	 */
	abstract public function queryProvider();

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

	/**
	 * @param string $idString
	 * @param DataValue|null $dataValue
	 */
	protected function insertItem( $idString, DataValue $dataValue = null ) {
		$item = Item::newEmpty();
		$item->setId( new ItemId( $idString ) );

		if ( $dataValue !== null ) {
			$mainSnak = new PropertyValueSnak( new PropertyId( 'P7701' ), $dataValue );

			$guid = 'statement' . $this->guidCounter;
			$this->guidCounter++;

			$item->getStatements()->addNewStatement( $mainSnak, null, null, $guid );
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
			new EntityIdValue( new PropertyId( 'P7701' ) ),
			new ValueDescription( $dataValue )
		);

		$queryEngine = $this->store->newQueryEngine();
		$options = new QueryOptions( 100, 0 );
		$entityIds = $queryEngine->getMatchingEntities( $someProperty, $options );

		$idStrings = $this->getIdStrings( $entityIds );
		$this->assertSameElements( $expectedIdStrings, $idStrings );
	}

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
