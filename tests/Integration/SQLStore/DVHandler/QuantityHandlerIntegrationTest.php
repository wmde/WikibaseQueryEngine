<?php

namespace Wikibase\QueryEngine\Tests\Integration\SQLStore\DVHandler;

use DataValues\DecimalValue;
use DataValues\QuantityValue;
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
class QuantityHandlerIntegrationTest extends DataValueHandlerIntegrationTest {

	public function getDataValueType() {
		return 'quantity';
	}

	protected function insertItems() {
		$this->insertItem( 'Q10' );

		$amount = new DecimalValue( 20 );
		$this->insertItem( 'Q20', new QuantityValue( $amount, '1', $amount, $amount ) );

		$amount = new DecimalValue( 21 );
		$this->insertItem( 'Q21', new QuantityValue( $amount, '1', $amount, $amount ) );
	}

	public function queryProvider() {
		$amount1 = new DecimalValue( 20 );
		$amount2 = new DecimalValue( 21 );

		return array(
			array(
				new QuantityValue( $amount1, '1', $amount1, $amount1 ),
				array( 'Q20' ),
			),
			array(
				new QuantityValue( $amount2, '1', $amount2, $amount2 ),
				array( 'Q21' ),
			),
		);
	}

}
