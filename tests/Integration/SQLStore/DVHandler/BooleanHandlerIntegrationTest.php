<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\BooleanValue;
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
class BooleanHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'boolean';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new BooleanValue( false ) );

		$this->insertItem( 'Q21', new BooleanValue( true ) );
	}

	public function queryProvider() {
		return array(
			array(
				new BooleanValue( false ),
				array( 'Q20' ),
			),
			array(
				new BooleanValue( true ),
				array( 'Q21' ),
			),
		);
	}

}
