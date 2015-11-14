<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\QueryEngine\Tests\Integration\SQLStore\DataValueHandlerIntegrationTest;

/**
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group WikibaseQueryEngineIntegration
 *
 * @group medium
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class EntityIdHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'wikibase-entityid';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new EntityIdValue( new ItemId( 'Q30' ) ) );

		$this->insertItem( 'Q21', new EntityIdValue( new ItemId( 'Q31' ) ) );
	}

	public function queryProvider() {
		return array(
			array(
				new EntityIdValue( new ItemId( 'Q30' ) ),
				array( 'Q20' ),
			),
			array(
				new EntityIdValue( new ItemId( 'Q31' ) ),
				array( 'Q21' ),
			),
		);
	}

}
