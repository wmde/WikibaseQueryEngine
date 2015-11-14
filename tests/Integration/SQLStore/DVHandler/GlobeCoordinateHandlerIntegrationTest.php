<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
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
class GlobeCoordinateHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'globecoordinate';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$this->insertItem( 'Q20', new GlobeCoordinateValue( new LatLongValue( 0, 20 ), null ) );

		$this->insertItem( 'Q21', new GlobeCoordinateValue( new LatLongValue( 0, 21 ), 10 ) );

		$this->insertItem( 'Q22', new GlobeCoordinateValue( new LatLongValue( 0, 22 ), 0.1 ) );

		$this->insertItem( 'Q23', new GlobeCoordinateValue( new LatLongValue( 0, 22 ), 0.1, 'Vulcan' ) );
	}

	public function queryProvider() {
		return array(
			array(
				new GlobeCoordinateValue( new LatLongValue( 0, 20 ), null ),
				array( 'Q20' ),
			),
			array(
				new GlobeCoordinateValue( new LatLongValue( 0, 21 ), null ),
				array( 'Q21' ),
			),
			array(
				new GlobeCoordinateValue( new LatLongValue( 0, 22 ), 0.1 ),
				array( 'Q22' ),
			),
			array(
				new GlobeCoordinateValue( new LatLongValue( 0, 22 ), 1 ),
				array( 'Q21', 'Q22' ),
			),
			array(
				new GlobeCoordinateValue( new LatLongValue( 0, 22 ), 0.1, 'Vulcan' ),
				array( 'Q23' ),
			),
		);
	}

}
