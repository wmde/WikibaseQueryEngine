<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use DataValues\QuantityValue;
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
class QuantityHandler extends DataValueHandler {

	/**
	 * @see DataValueHandler::getBaseTableName
	 */
	protected function getBaseTableName() {
		return 'quantity';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value_actual', Type::FLOAT );
		$table->addColumn( 'value_lower_bound', Type::FLOAT );
		$table->addColumn( 'value_upper_bound', Type::FLOAT );
		$table->addColumn( 'hash', Type::STRING, array( 'length' => 32 ) );

		$table->addIndex( array( 'value_actual' ) );
		// TODO: This index is currently not used. Does it make sense to introduce it anyway?
		// I still do not fully understand what MySQL does when a combined index is queried
		// with multiple lower/greater than clauses. Maybe separate indexes are better?
		$table->addIndex( array( 'value_lower_bound', 'value_upper_bound' ) );
	}

	/**
	 * @see DataValueHandler::getInsertValues
	 *
	 * @param DataValue $value
	 *
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function getInsertValues( DataValue $value ) {
		if ( !( $value instanceof QuantityValue ) ) {
			throw new InvalidArgumentException( 'Value is not a QuantityValue.' );
		} elseif ( $value->getUnit() !== '1' ) {
			throw new InvalidArgumentException( 'Units other than "1" are not yet supported.' );
		}

		$values = array(
			'value_actual' => $value->getAmount()->getValue(),
			'value_lower_bound' => $value->getLowerBound()->getValue(),
			'value_upper_bound' => $value->getUpperBound()->getValue(),

			'hash' => $this->getEqualityFieldValue( $value ),
		);

		return $values;
	}

}
