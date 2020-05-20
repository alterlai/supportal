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

    public function name($object, PropertyMapping $mapping): string
    {
        return "testnaam2.pdf";
    }
}