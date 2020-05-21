<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class DraftNamer implements NamerInterface
{
//    private $logger;
//
//    public function __construct(LoggerInterface $logger)
//    {
//        $this->logger = $logger;
//    }

    /**
     * @param object $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function name($object, PropertyMapping $mapping): string
    {
        $building = $object->getBuilding()->getCode();
        $floor = $object->getFloor();
        $discipline = $object->getDiscipline()->getCode();
        $doctype = $object->getDocumentType()->getCode();
        $version = $object->getVersion();

        return $building. "-". $floor. "-". $discipline.".". $doctype. "-000-". $version.".dwg";
    }
}