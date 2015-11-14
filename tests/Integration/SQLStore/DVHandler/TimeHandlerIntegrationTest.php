<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\TimeValue;
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
class TimeHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'time';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new TimeValue(
			'+20-00-00T00:00:00Z',
			0, 0, 0, TimeValue::PRECISION_YEAR, 'Stardate'
		) );

		$this->insertItem( 'Q21', new TimeValue(
			'+21-00-00T00:00:00Z',
			0, 0, 0, TimeValue::PRECISION_YEAR, 'Stardate'
		) );
	}

	public function queryProvider() {
		return array(
			array(
				new TimeValue(
					'+20-00-00T00:00:00Z',
					0, 0, 0, TimeValue::PRECISION_YEAR, 'Stardate'
				),
				array( 'Q20' ),
			),
			array(
				new TimeValue(
					'+21-00-00T00:00:00Z',
					0, 0, 0, TimeValue::PRECISION_YEAR, 'Stardate'
				),
				array( 'Q21' ),
			),
		);
	}

}
