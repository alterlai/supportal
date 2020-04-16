<?php

namespace App\DataFixtures;

use App\Entity\Discipline;
use Doctrine\Bundle\FixturesBundle\Fixture;
use League\Csv\Reader;
use Doctrine\Common\Persistence\ObjectManager;

class DisciplineFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/nlsfb.csv', 'r');

        $reader->setDelimiter(";");

        $headers = $reader->fetchOne();     // Get headers

        $reader->setHeaderOffset(0);    // skip header row

        $data = $reader->getRecords($headers);

        foreach($data as  $row)
        {
            $discipline = (new Discipline())
                ->setDescription($row['description'])
                ->setCode((float) $row['code']);
            $manager->persist($discipline);
        }

        $manager->flush();
    }

}
