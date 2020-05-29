<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\DocumentDraft;
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
        /** @var Document $document */
        $document = $object->getDocument();
        $building = $document->getBuilding()->getCode();
        $floor = $document->getFloor();
        $discipline = $document->getDiscipline()->getCode();
        $doctype = $document->getDocumentType()->getCode();
        $version = $document->getVersion();

        return $building. "-". $floor. "-". $discipline.".". $doctype. "-000-". $version.".dwg";
    }
}