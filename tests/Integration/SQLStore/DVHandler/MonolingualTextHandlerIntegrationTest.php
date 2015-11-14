<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\MonolingualTextValue;
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
class MonolingualTextHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'monolingualtext';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new MonolingualTextValue( 'en', 'A' ) );

		$this->insertItem( 'Q21', new MonolingualTextValue( 'en', 'B' ) );
	}

	public function queryProvider() {
		return array(
			array(
				new MonolingualTextValue( 'en', 'A' ),
				array( 'Q20' ),
			),
			array(
				new MonolingualTextValue( 'en', 'B' ),
				array( 'Q21' ),
			),
		);
	}

}
