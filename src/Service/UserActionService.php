<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\User;
use App\Entity\UserAction;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserActionService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User|UserInterface $user
     * @param Document $document
     * @param DateTimeImmutable|null $deadline
     * @param string $filetype
     * @param bool $hasDeadline
     * @throws \Exception
     */
    public function createUserAction(User $user, Document $document, ?DateTimeImmutable $deadline, string $filetype, bool $hasDeadline)
    {
        $userAction = new UserAction();
        $userAction->setUser($user);
        $userAction->setDocument($document);
        $userAction->setDownloadedAt(new DateTimeImmutable("now"));
        $userAction->setFileType($filetype);
        if ($hasDeadline)
        {
            $userAction->setDeadline($deadline);
        }

        $this->entityManager->persist($userAction);
        $this->entityManager->flush();
    }
}