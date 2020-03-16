<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ViewHistoryRepository")
 */
class ViewHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="viewHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action_type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\document")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getActionType(): ?actions
    {
        return $this->action_type;
    }

    public function setActionType(?actions $action_type): self
    {
        $this->action_type = $action_type;

        return $this;
    }

    public function getDocumentId(): ?document
    {
        return $this->document_id;
    }

    public function setDocumentId(?document $document_id): self
    {
        $this->document_id = $document_id;

        return $this;
    }
}
