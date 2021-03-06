<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\Issue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueHandlerService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addDocumentIssue(Document $document, UserInterface $user, \DateTimeImmutable $deadline)
    {
        $issue = new Issue();
        $issue->setDocument($document);
        $issue->setIssuedTo($user);
        $issue->setIssuedAt(new \DateTimeImmutable("now"));
        $issue->setIssueDeadline($deadline);
        $issue->setClosed(0);
        $this->entityManager->persist($issue);
        $this->entityManager->flush();
    }
}