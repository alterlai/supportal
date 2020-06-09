<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\User;
use App\Entity\UserAction;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class UserActionService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createUserAction(User $user, Document $document, ?DateTimeImmutable $deadline, string $filetype)
    {
        $userAction = new UserAction();
        $userAction->setUser($user);
        $userAction->setDocument($document);
        $userAction->setDownloadedAt(new DateTimeImmutable("now"));
        $userAction->setDeadline($deadline);
        $userAction->setFileType($filetype);

        $this->entityManager->persist($userAction);
        $this->entityManager->flush();
    }
}