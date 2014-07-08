<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\GlobeCoordinateValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;

/**
 * @since 0.3
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
class GlobeCoordinateHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'globecoordinate';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value_globe',   Type::STRING, array( 'length' => 255 ) );
		$table->addColumn( 'value_lat',     Type::FLOAT );
		$table->addColumn( 'value_lon',     Type::FLOAT );
		$table->addColumn( 'value_min_lat', Type::FLOAT );
		$table->addColumn( 'value_max_lat', Type::FLOAT );
		$table->addColumn( 'value_min_lon', Type::FLOAT );
		$table->addColumn( 'value_max_lon', Type::FLOAT );
		$table->addColumn( 'hash',          Type::STRING, array( 'length' => 32 ) );

		// TODO: We still need to find out if combined indexes are better or not.
		$table->addIndex( array( 'value_lon', 'value_lat' ) );
		$table->addIndex( array( 'value_min_lat', 'value_max_lat', 'value_min_lon', 'value_max_lon' ) );
	}

	/**
	 * @see DataValueHandler::getSortFieldNames
	 */
	public function getSortFieldNames() {
		// Order by West-East first
		return array( 'value_lon', 'value_lat' );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof GlobeCoordinateValue ) ) {
			throw new InvalidArgumentException( 'Value is not a GlobeCoordinateValue.' );
		}

		$lat = $value->getLatitude();
		$lon = $value->getLongitude();
		$precision = abs( $value->getPrecision() );

		$values = array(
			'value_globe'   => $value->getGlobe(),
			'value_lat'     => $lat,
			'value_lon'     => $lon,
			'value_min_lat' => $lat - $precision,
			'value_max_lat' => $lat + $precision,
			'value_min_lon' => $lon - $precision,
			'value_max_lon' => $lon + $precision,

			// No special human-readable hash needed, everything required is in the other fields.
			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

}
