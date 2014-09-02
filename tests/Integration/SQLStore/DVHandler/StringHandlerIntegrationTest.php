<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\StringValue;
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
class StringHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'string';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new StringValue( 'A' ) );

		$this->insertItem( 'Q21', new StringValue( 'B' ) );
	}

	public function queryProvider() {
		return array(
			array(
				new StringValue( 'A' ),
				array( 'Q20' ),
			),
			array(
				new StringValue( 'B' ),
				array( 'Q21' ),
			),
		);
	}

}
