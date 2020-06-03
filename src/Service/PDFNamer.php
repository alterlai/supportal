<?php

namespace App\Service;

use App\Entity\Document;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class PDFNamer implements NamerInterface
{
//    private $logger;
//
//    public function __construct(LoggerInterface $logger)
//    {
//        $this->logger = $logger;
//    }

    /**
     * @param Document $object
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

        return $building. "-". $floor. "-". $discipline.".". $doctype. "-000-".$version.".pdf";
    }
}