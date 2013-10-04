<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimInserter;
use Wikibase\QueryEngine\SQLStore\ClaimStore\ClaimRowBuilder;
use Wikibase\QueryEngine\SQLStore\Engine\DescriptionMatchFinder;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakInserter;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRemover;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakRowBuilder;
use Wikibase\QueryEngine\SQLStore\SnakStore\SnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValuelessSnakStore;
use Wikibase\QueryEngine\SQLStore\SnakStore\ValueSnakStore;
use Wikibase\SnakRole;

/**
 * SQLStore component factory.
 * This class is private to the SQLStore component and should not be access from the outside.
 * It is furthermore intended to contain construction logic needed by the Store class while
 * it should not be publicly exposed there. This factory should thus not be passed to or
 * constructed in deeper parts of the SQLStore.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class Factory {

	protected $config;
	protected $queryInterface;

	/**
	 * @var Schema|null
	 */
	protected $schema = null;

	public function __construct( StoreConfig $config, QueryInterface $queryInterface ) {
		$this->config = $config;
		$this->queryInterface = $queryInterface;
	}

	/**
	 * @return Schema
	 */
	public function getSchema() {
		if ( $this->schema === null ) {
			$this->schema = new Schema( $this->config );
		}

		return $this->schema;
	}

	public function newEntityInserter() {
		return new EntityInserter(
			$this->newClaimInserter()
		);
	}

	public function newEntityUpdater() {
		return new EntityUpdater(
			$this->newEntityRemover(),
			$this->newEntityInserter()
		);
	}

	public function newEntityRemover() {
		return new EntityRemover(
			$this->newSnakRemover()
		);
	}

	public function newSnakRemover() {
		return new SnakRemover( $this->getSnakStores() );
	}

	public function newEntityTable() {
		return new EntityTable(
			$this->queryInterface,
			$this->getSchema()->getEntitiesTable()->getName()
		);
	}

	public function newClaimInserter() {
		return new ClaimInserter(
			$this->newSnakInserter(),
			new ClaimRowBuilder()
		);
	}

	public function newSnakInserter() {
		return new SnakInserter(
			$this->getSnakStores(),
			new SnakRowBuilder()
		);
	}

	/**
	 * @return SnakStore[]
	 */
	protected function getSnakStores() {
		return array(
			new ValueSnakStore(
				$this->queryInterface,
				$this->getSchema()->getDataValueHandlers( SnakRole::MAIN_SNAK ),
				SnakRole::MAIN_SNAK
			),
			new ValueSnakStore(
				$this->queryInterface,
				$this->getSchema()->getDataValueHandlers( SnakRole::QUALIFIER ),
				SnakRole::QUALIFIER
			),
			new ValuelessSnakStore(
				$this->queryInterface,
				$this->getSchema()->getValuelessSnaksTable()->getName()
			)
		);
	}

	public function newWriter() {
		return new Writer(
			$this->newEntityInserter(),
			$this->newEntityUpdater(),
			$this->newEntityRemover()
		);
	}

	/**
	 * @return DescriptionMatchFinder
	 */
	public function newDescriptionMatchFinder() {
		return new DescriptionMatchFinder(
			$this->queryInterface,
			$this->getSchema(),
			$this->config->getPropertyDataValueTypeLookup(),
			new BasicEntityIdParser() // TODO: inject
		);
	}

}
