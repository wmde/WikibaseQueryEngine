<?php

namespace Wikibase\QueryEngine\Tests\Phpunit\SQLStore\Setup;

use Psr\Log\NullLogger;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\Setup\Uninstaller;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @covers Wikibase\QueryEngine\SQLStore\Setup\Uninstaller
 *
 * @group Wikibase
 * @group WikibaseQueryEngine
 * @group StoreSchema
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class UninstallerTest extends \PHPUnit_Framework_TestCase {

	public function testUninstall() {
		$handlers = new DataValueHandlers();
		$handlers->addMainSnakHandler( 'string', new StringHandler() );
		$handlers->addQualifierHandler( 'string', new StringHandler() );

		$schemaManager = $this->getMockBuilder( 'Doctrine\DBAL\Schema\AbstractSchemaManager' )
			->disableOriginalConstructor()->setMethods( array( 'dropTable' ) )->getMockForAbstractClass();

		$schemaManager->expects( $this->atLeastOnce() )
			->method( 'dropTable' )
			->will( $this->returnValue( true ) );

		$storeSetup = new Uninstaller(
			new NullLogger(),
			new StoreSchema( 'prefix_', $handlers ),
			$schemaManager
		);

		$storeSetup->uninstall();
	}

}
