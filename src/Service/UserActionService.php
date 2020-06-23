<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\User;
use App\Entity\UserAction;
use App\Exception\InvalidDeadlineException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserActionService
{
    private $entityManager;
    private $max_revision_time;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->max_revision_time = $parameterBag->get('max_revision_time');

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
            $this->validateDeadline($deadline);
            $userAction->setDeadline($deadline);
        }

        $this->entityManager->persist($userAction);
        $this->entityManager->flush();
    }

    /**
     * Validate the given deadline.
     * Deadline cannot be in the past.
     * Deadline cannot be longer than 1 month.
     * @param DateTimeImmutable|null $deadline
     * @throws InvalidDeadlineException
     * @throws \Exception
     */
    private function validateDeadline(?DateTimeImmutable $deadline)
    {
        $now = new \DateTimeImmutable("now");

        $max_deadline = new \DateTimeImmutable($this->max_revision_time);

        if ($deadline < $now)
        {
            throw new InvalidDeadlineException("Deadline kan niet in het verleden zijn.");
        }

        if ($deadline > $max_deadline)
        {
            throw new InvalidDeadlineException("Deadline kan niet langer zijn dan 1 maand");
        }
    }
}