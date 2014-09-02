<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\NumberValue;
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
class NumberHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'number';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new NumberValue( 20 ) );

		$this->insertItem( 'Q21', new NumberValue( 21 ) );
	}

	public function queryProvider() {
		return array(
			array(
				new NumberValue( 20 ),
				array( 'Q20' ),
			),
			array(
				new NumberValue( 21 ),
				array( 'Q21' ),
			),
		);
	}

}
