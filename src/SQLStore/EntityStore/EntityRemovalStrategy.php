<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityRemovalStrategy {

	public function removeEntity( EntityDocument $entity );

}
