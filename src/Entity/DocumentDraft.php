<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentDraftRepository")
 */
class DocumentDraft
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Document")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="documentDrafts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uploaded_by;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploaded_at;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rejection_description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $rejected_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentId(): ?Document
    {
        return $this->document_id;
    }

    public function setDocumentId(?Document $document_id): self
    {
        $this->document_id = $document_id;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploaded_by;
    }

    public function setUploadedBy(?User $uploaded_by): self
    {
        $this->uploaded_by = $uploaded_by;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploaded_at;
    }

    public function setUploadedAt(\DateTimeInterface $uploaded_at): self
    {
        $this->uploaded_at = $uploaded_at;

        return $this;
    }

    public function getRejectionDescription(): ?string
    {
        return $this->rejection_description;
    }

    public function setRejectionDescription(?string $rejection_description): self
    {
        $this->rejection_description = $rejection_description;

        return $this;
    }

    public function getRejectedAt(): ?\DateTimeInterface
    {
        return $this->rejected_at;
    }

    public function setRejectedAt(?\DateTimeInterface $rejected_at): self
    {
        $this->rejected_at = $rejected_at;

        return $this;
    }
}
