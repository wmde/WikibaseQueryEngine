<?php

namespace Wikibase\QueryEngine\SQLStore\Setup;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Psr\Log\LoggerInterface;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreUpdater;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Updater implements QueryStoreUpdater {

	private $logger;
	private $storeSchema;
	private $schemaManager;

	public function __construct( LoggerInterface $logger, StoreSchema $storeSchema, AbstractSchemaManager $schemaManager ) {
		$this->logger = $logger;
		$this->storeSchema = $storeSchema;
		$this->schemaManager = $schemaManager;
	}

	/**
	 * @see QueryStoreUpdater::update
	 *
	 * @throws QueryEngineException
	 */
	public function update() {
		foreach ( $this->storeSchema->getTables() as $table ) {
			try {
				$this->handleTable( $table );
			}
			catch ( DBALException $ex ) {
				$this->logger->alert( $ex->getMessage(), array( 'exception' => $ex ) );
			}
		}
	}

	private function handleTable( Table $table ) {
		if ( $this->schemaManager->tablesExist( array( $table->getName() ) ) ) {
			$this->migrateTable( $table );
		}
		else {
			$this->createTable( $table );
		}
	}

	private function createTable( Table $table ) {
		$this->schemaManager->createTable( $table );
	}

	private function migrateTable( Table $table ) {
		$comparator = new Comparator();

		$tableDiff = $comparator->diffTable(
			$this->schemaManager->listTableDetails( $table->getName() ),
			$table
		);

		if ( $tableDiff !== false ) {
			$this->schemaManager->alterTable( $tableDiff );
		}
	}

}
