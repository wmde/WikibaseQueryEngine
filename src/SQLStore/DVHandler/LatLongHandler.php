<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\LatLongValue;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * Represents the mapping between DataValues\LatLongValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LatLongHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::newDataValueFromValueField
	 *
	 * @since 0.1
	 *
	 * @param string $valueFieldValue
	 *
	 * @return DataValue
	 */
	public function newDataValueFromValueField( $valueFieldValue ) {
		$value = explode( '|', $valueFieldValue, 2 );
		return new LatLongValue( (float)$value[0], (float)$value[1] );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof LatLongValue ) ) {
			throw new InvalidArgumentException( 'Value is not a LatLongValue' );
		}

		$values = array(
			'value_lat' => $value->getLatitude(),
			'value_lon' => $value->getLongitude(),

			'value' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

	/**
	 * @see DataValueHandler::getEqualityFieldValue
	 *
	 * @param DataValue $value
	 *
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		if ( !( $value instanceof LatLongValue ) ) {
			throw new InvalidArgumentException( 'Value is not a LatLongValue' );
		}

		return $value->getLatitude() . '|' . $value->getLongitude();
	}

}