<?php

namespace Wikibase\QueryEngine\Console\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\QueryEngine\Importer\EntitiesImporter;
use Wikibase\QueryEngine\Importer\EntitiesImporterBuilder;

/**
 * @since 0.3
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportEntitiesCommand extends Command {

	/**
	 * @var EntitiesImporterBuilder
	 */
	private $importerBuilder;

	public function setDependencies( EntitiesImporterBuilder $importerBuilder ) {
		$this->importerBuilder = $importerBuilder;
	}

	protected function configure() {
		$this->setName( 'import' );
		$this->setDescription( 'Imports a collection of entities into the QueryEngine store' );

		$this->addOption(
			'batchsize',
			'b',
			InputOption::VALUE_OPTIONAL,
			'The number of entities to handle in one go',
			10
		);

		$this->addOption(
			'continue',
			'c',
			InputOption::VALUE_OPTIONAL,
			'The id of the entity to resume from (id not included)'
		);

		$this->addOption(
			'limit',
			'l',
			InputOption::VALUE_OPTIONAL,
			'The maximum number of entities to import'
		);
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->importerBuilder->setBatchSize( (int)$input->getOption( 'batchsize' ) );
		$this->importerBuilder->setLimit( (int)$input->getOption( 'limit' ) );
		$this->importerBuilder->setReporter( new CliImportReporter( $output ) );

		$this->handleContinueOption( $input, $output );

		$importer = $this->importerBuilder->newImporter();

		$this->registerSignalHandlers( $importer, $output );

		$importer->run();
	}

	private function handleContinueOption( InputInterface $input, OutputInterface $output ) {
		$continueId = $input->getOption( 'continue' );

		if ( $continueId !== null ) {
			$output->writeln( "<info>Continuing from </info><comment>$continueId</comment>" );
			$this->importerBuilder->setContinuationId( $continueId );
		}
	}

	private function registerSignalHandlers( EntitiesImporter $importer, OutputInterface $output ) {
		if ( function_exists( 'pcntl_signal' ) ) {
			pcntl_signal( SIGINT, array( $importer, 'stop' ) );
			pcntl_signal( SIGTERM, array( $importer, 'stop' ) );
		}
		else {
			$output->writeln( '<comment>PCNTL not available; running without graceful interruption support</comment>' );
		}
	}

}
