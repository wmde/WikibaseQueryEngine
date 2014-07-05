<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DVHandler;

use DataValues\GlobeCoordinateValue;
use DataValues\LatLongValue;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\GlobeCoordinateHandler;
use Wikibase\QueryEngine\Tests\Phpunit\SQLStore\DataValueHandlerTest;

/**
 * @covers Wikibase\QueryEngine\SQLStore\DVHandler\GlobeCoordinateHandler
 *
 * @ingroup WikibaseQueryEngineTest
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class GlobeCoordinateHandlerTest extends DataValueHandlerTest {

	/**
	 * @see DataValueHandlerTest::getInstances
	 *
	 * @return DataValueHandler[]
	 */
	protected function getInstances() {
		$instances = array();

		$instances[] = new GlobeCoordinateHandler();

		return $instances;
	}

	/**
	 * @see DataValueHandlerTest::getValues
	 *
	 * @return GlobeCoordinateValue[]
	 */
	protected function getValues() {
		$values = array();

		foreach ( array( 0, -1/3, 2/3, 99 ) as $latitude ) {
			foreach ( array( 0, -1/3, 2/3, 99 ) as $longitude ) {
				foreach ( array( null, 0, 0.01/3, -1 ) as $precision ) {
					foreach ( array( GlobeCoordinateValue::GLOBE_EARTH, 'Vulcan' ) as $globe ) {
						$values[] = new GlobeCoordinateValue(
							new LatLongValue( $latitude, $longitude ),
							$precision,
							$globe
						);
					}
				}
			}
		}

		return $values;
	}

}
