<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\SnakStore;

use DataValues\StringValue;
use Wikibase\Database\Schema\Definitions\FieldDefinition;
use Wikibase\Database\Schema\Definitions\TableDefinition;
use Wikibase\Database\Schema\Definitions\TypeDefinition;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\QueryEngine\SQLStore\DataValueTable;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakRow;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;
use Wikibase\DataModel\Snak\SnakRole;

/**
 * @covers Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseSnakStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ValueSnakStoreTest extends SnakStoreTest {

	protected function getInstance() {
		return new ValueSnakStore(
			$this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' ),
			array(
				'string' => $this->newStringHandler()
			),
			SnakRole::MAIN_SNAK
		);
	}

	protected function newStringHandler() {
		// FIXME: should not have a partial copy of this
		return new StringHandler( new DataValueTable(
			new TableDefinition(
				'strings_of_doom',
				array(
					new FieldDefinition(
						'value',
						new TypeDefinition( TypeDefinition::TYPE_BLOB ),
						false
					),
				)
			),
			'value',
			'value',
			'value',
			'value'
		) );
	}

	public function canStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::MAIN_SNAK,
			'Q100'
		) );


		return $argLists;
	}

	public function cannotStoreProvider() {
		$argLists = array();

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P1',
			SnakRole::QUALIFIER,
			'Q1'
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_NO_VALUE,
			'P1',
			SnakRole::MAIN_SNAK,
			'Q1'
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P1',
			SnakRole::QUALIFIER,
			'Q1'
		) );

		$argLists[] = array( new ValuelessSnakRow(
			ValuelessSnakRow::TYPE_SOME_VALUE,
			'P1',
			SnakRole::MAIN_SNAK,
			'Q1'
		) );

		$argLists[] = array( new ValueSnakRow(
			new StringValue( 'nyan' ),
			'P1',
			SnakRole::QUALIFIER,
			'Q100'
		) );

		return $argLists;
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnak( ValueSnakRow $snakRow ) {
		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$stringHandler = $this->newStringHandler();

		$queryInterface->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->equalTo( 'strings_of_doom' ),
				$this->equalTo(
					array_merge(
						array(
							'property_id' => $snakRow->getPropertyId(),
							'entity_id' => $snakRow->getSubjectId(),
						),
						$stringHandler->getInsertValues( $snakRow->getValue() )
					)
				)
			);

		$store = new ValueSnakStore(
			$queryInterface,
			array(
				'string' => $stringHandler
			),
			SnakRole::MAIN_SNAK
		);

		$store->storeSnakRow( $snakRow );
	}

	/**
	 * @dataProvider canStoreProvider
	 */
	public function testStoreSnakWithUnknownValueType( ValueSnakRow $snakRow ) {
		$this->setExpectedException( 'OutOfBoundsException' );

		$store = new ValueSnakStore(
			$this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' ),
			array(),
			SnakRole::MAIN_SNAK
		);

		$store->storeSnakRow( $snakRow );
	}

	public function testRemoveSnaksOfSubject() {
		$subjectId = 'Q4242';

		$stringHandler = $this->newStringHandler();

		$queryInterface = $this->getMock( 'Wikibase\Database\QueryInterface\QueryInterface' );

		$queryInterface->expects( $this->atLeastOnce() )
			->method( 'delete' )
			->with(
				$this->equalTo( $stringHandler->getDataValueTable()->getTableDefinition()->getName() ),
				$this->equalTo( array( 'entity_id' => $subjectId ) )
			);

		$store = new ValueSnakStore(
			$queryInterface,
			array(
				'string' => $stringHandler
			),
			SnakRole::MAIN_SNAK
		);

		$store->removeSnaksOfSubject( new ItemId( $subjectId ) );
	}

}