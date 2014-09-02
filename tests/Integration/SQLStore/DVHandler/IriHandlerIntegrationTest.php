<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\IriValue;
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
class IriHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'iri';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new IriValue( 'http', '//www.wikidata.org/' ) );

		$this->insertItem( 'Q21', new IriValue( 'http', '//en.wikipedia.org/' ) );
	}

	public function queryProvider() {
		return array(
			array(
				new IriValue( 'http', '//www.wikidata.org/' ),
				array( 'Q20' ),
			),
			array(
				new IriValue( 'http', '//en.wikipedia.org/' ),
				array( 'Q21' ),
			),
		);
	}

}
