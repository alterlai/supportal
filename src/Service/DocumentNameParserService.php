<?php

namespace App\Service;


use App\Entity\Building;
use App\Entity\Discipline;
use App\Entity\DocumentType;

/**
 * Class DocumentManagerService
 *
 * Document names are structured as follows:
 * 3 characters: abbreviation of the building
 * 2 digits: floor
 * 2 digits *dot* 2 digits: document group number
 * @package App\Service
 */
class DocumentNameParserService
{
    public function generateFileNameFromEntities(Building $building, Discipline $discipline, DocumentType $documentType, string $floor, int $revision, string $fileExtension)
    {
        $name = (string) $building->getCode() . "-" . $floor . "-" . $discipline->getCode() . "." . $documentType->getCode() . "-000-" . $revision . $fileExtension;
        return $name;
    }

}