<?php

namespace App\Service;


use App\Entity\Building;
use App\Entity\Discipline;

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
    public function getDocumentFromFilename()
    {
        return null;
    }

    public function generateFileNameFromEntities(Building $building, Discipline $discipline, int $floor, int $revision)
    {
        $name = (string) $building->getCode() . "-" . $floor . "-" . $discipline->getCode() . "." . "11" . "-000-" . $revision . ".dwg";
        return $name;
    }

}