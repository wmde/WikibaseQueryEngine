<?php

namespace Wikibase\QueryEngine\SQLStore;

use Ask\Language\Description\ValueDescription;
use DataValues\DataValue;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;
use RuntimeException;
use Wikibase\QueryEngine\QueryNotSupportedException;

/**
 * Represents the mapping between a DataValue type and the
 * associated implementation in the store.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DataValueHandler {

	/**
	 * Needs to be set by the constructor.
	 *
	 * @var string|null
	 */
	protected $tableName = null;

	/**
	 * Returns the full name of the table.
	 * This is the same value as ->constructTable()->getName().
	 *
	 * @return string
	 *
	 * @throws RuntimeException
	 */
	public function getTableName() {
		if ( $this->tableName === null ) {
			throw new RuntimeException(
				'Cannot get the table name when the table name prefix has not been set yet'
			);
		}

		return $this->tableName;
	}

	/**
	 * Prefixes the table name. This needs to be called once, and only once,
	 * before getTableName or constructTable are called.
	 *
	 * @param string $tablePrefix
	 *
	 * @throws RuntimeException
	 */
	public function setTablePrefix( $tablePrefix ) {
		if ( $this->tableName !== null ) {
			throw new RuntimeException( 'Cannot set the table name prefix more than once' );
		}

		$this->tableName = $tablePrefix . $this->getBaseTableName();
	}

	/**
	 * Returns a Table object that represents the schema of the data value table.
	 *
	 * @return Table
	 */
	public function constructTable() {
		$table = new Table( $this->getTableName() );

		$this->completeTable( $table );

		return $table;
	}

	/**
	 * Returns the base name of the table.
	 * This does not contain any prefixes indicating which store it
	 * belongs to or what the role of the data value it handles is.
	 *
	 * @return string
	 */
	abstract protected function getBaseTableName();

	/**
	 * @param Table $table
	 */
	abstract protected function completeTable( Table $table );

	/**
	 * Returns the name of the field that holds a value suitable for equality checks.
	 *
	 * This field should not exceed 255 chars index space equivalent.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getEqualityFieldName() {
		return 'hash';
	}

	/**
	 * Returns the names of the fields used to order this type of DataValue.
	 * Usually a single field. Must not be an empty array.
	 *
	 * @since 0.1
	 *
	 * @return string[]
	 */
	public function getSortFieldNames() {
		return array( $this->getEqualityFieldName() );
	}

	/**
	 * Return an array of fields=>values that is to be inserted when
	 * writing the given DataValue to the database. Values should be set
	 * for all columns, even if NULL. This array is used to perform all
	 * insert operations into the DB.
	 *
	 * The passed DataValue needs to be of a type supported by the DataValueHandler.
	 * If it is not supported, an InvalidArgumentException might be thrown.
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	abstract public function getInsertValues( DataValue $value );

	/**
	 * Returns the equality field value for a given data value.
	 * This value is needed for constructing equality checking
	 * queries.
	 *
	 * @since 0.1
	 *
	 * @param DataValue $value
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function getEqualityFieldValue( DataValue $value ) {
		return $value->getHash();
	}

	/**
	 * @since 0.2
	 *
	 * @param QueryBuilder $builder
	 * @param ValueDescription $description
	 *
	 * @throws InvalidArgumentException
	 * @throws QueryNotSupportedException
	 */
	public function addMatchConditions( QueryBuilder $builder, ValueDescription $description ) {
		if ( $description->getComparator() === ValueDescription::COMP_EQUAL ) {
			$builder->andWhere( $this->getEqualityFieldName() . '= :equality' );
			$builder->setParameter( ':equality', $this->getEqualityFieldValue( $description->getValue() ) );
		}
		else {
			throw new QueryNotSupportedException( $description, 'Only equality is supported' );
		}
	}

}
