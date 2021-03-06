<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\QueryEngine\SQLStore\DVHandler\BooleanHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\EntityIdHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\GlobeCoordinateHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\IriHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\LatLongHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\MonolingualTextHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\QuantityHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\StringHandler;
use Wikibase\QueryEngine\SQLStore\DVHandler\TimeHandler;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DataValueHandlersBuilder {

	/**
	 * @var DataValueHandlers
	 */
	private $handlers;

	public function __construct() {
		$this->handlers = new DataValueHandlers();
	}

	/**
	 * @since 0.3
	 * @return self
	 */
	public function withSimpleHandlers() {
		return $this->withSimpleMainSnakHandlers()->withSimpleQualifierHandlers();
	}

	/**
	 * @since 0.3
	 * @return self
	 */
	public function withSimpleMainSnakHandlers() {
		$this->handlers->addMainSnakHandler( 'boolean', new BooleanHandler() );
		$this->handlers->addMainSnakHandler( 'iri', new IriHandler() );
		$this->handlers->addMainSnakHandler( 'geocoordinate', new LatLongHandler() );
		$this->handlers->addMainSnakHandler( 'globecoordinate', new GlobeCoordinateHandler() );
		$this->handlers->addMainSnakHandler( 'monolingualtext', new MonolingualTextHandler() );
		$this->handlers->addMainSnakHandler( 'number', new NumberHandler() );
		$this->handlers->addMainSnakHandler( 'quantity', new QuantityHandler() );
		$this->handlers->addMainSnakHandler( 'string', new StringHandler() );
		$this->handlers->addMainSnakHandler( 'time', new TimeHandler() );
		$this->handlers->addMainSnakHandler( 'wikibase-entityid', new EntityIdHandler() );

		return $this;
	}

	/**
	 * @since 0.3
	 * @return self
	 */
	public function withSimpleQualifierHandlers() {
		$this->handlers->addQualifierHandler( 'boolean', new BooleanHandler() );
		$this->handlers->addQualifierHandler( 'iri', new IriHandler() );
		$this->handlers->addQualifierHandler( 'geocoordinate', new LatLongHandler() );
		$this->handlers->addQualifierHandler( 'globecoordinate', new GlobeCoordinateHandler() );
		$this->handlers->addQualifierHandler( 'monolingualtext', new MonolingualTextHandler() );
		$this->handlers->addQualifierHandler( 'number', new NumberHandler() );
		$this->handlers->addQualifierHandler( 'quantity', new QuantityHandler() );
		$this->handlers->addQualifierHandler( 'string', new StringHandler() );
		$this->handlers->addQualifierHandler( 'time', new TimeHandler() );
		$this->handlers->addQualifierHandler( 'wikibase-entityid', new EntityIdHandler() );

		return $this;
	}

	/**
	 * @since 0.3
	 * @return DataValueHandlers
	 */
	public function getHandlers() {
		return $this->handlers;
	}

}
