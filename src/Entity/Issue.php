<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IssueRepository")
 */
class Issue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Document", inversedBy="issue")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document;

    /**
     * @ORM\Column(type="datetime")
     */
    private $issued_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $issue_deadline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="issues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $issued_to;

    /**
     * @ORM\Column(type="boolean")
     */
    private $closed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getIssuedAt(): ?\DateTimeInterface
    {
        return $this->issued_at;
    }

    public function setIssuedAt(\DateTimeInterface $issued_at): self
    {
        $this->issued_at = $issued_at;

        return $this;
    }

    public function getIssueDeadline(): ?\DateTimeInterface
    {
        return $this->issue_deadline;
    }

    public function setIssueDeadline(\DateTimeInterface $issue_deadline): self
    {
        $this->issue_deadline = $issue_deadline;

        return $this;
    }

    public function getIssuedTo(): ?User
    {
        return $this->issued_to;
    }

    public function setIssuedTo(?User $issued_to): self
    {
        $this->issued_to = $issued_to;

        return $this;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }
}
