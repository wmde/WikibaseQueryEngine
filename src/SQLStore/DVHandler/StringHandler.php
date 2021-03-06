<?php

namespace Wikibase\QueryEngine\SQLStore\DVHandler;

use DataValues\DataValue;
use DataValues\StringValue;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Wikibase\QueryEngine\SQLStore\DataValueHandler;
use Wikibase\QueryEngine\StringHasher;

/**
 * Represents the mapping between StringValue and
 * the corresponding table in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StringHandler extends DataValueHandler {

	/**
	 * @var StringHasher|null
	 */
	private $stringHasher = null;

	/**
	 * @see DataValueHandler::getBaseTableName
	 *
	 * @return string
	 */
	protected function getBaseTableName() {
		return 'string';
	}

	/**
	 * @see DataValueHandler::completeTable
	 */
	protected function completeTable( Table $table ) {
		$table->addColumn( 'value', Type::TEXT );
		$table->addColumn( 'hash', Type::STRING, array( 'length' => StringHasher::LENGTH ) );

		// TODO: Is an index on the first 255 bytes/chars of each BLOB/CLOB column possible?
		$table->addIndex( array( 'hash' ) );
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
		if ( !( $value instanceof StringValue ) ) {
			throw new InvalidArgumentException( 'Value is not a StringValue.' );
		}

		$values = array(
			'value' => $value->getValue(),

			'hash' => $this->getEqualityFieldValue( $value ),
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
		if ( !( $value instanceof StringValue ) ) {
			throw new InvalidArgumentException( 'Value is not a StringValue.' );
		}

		return $this->hash( $value->getValue() );
	}

	private function hash( $string ) {
		if ( $this->stringHasher === null ) {
			$this->stringHasher = new StringHasher();
		}

		return $this->stringHasher->hash( $string );
	}

}
