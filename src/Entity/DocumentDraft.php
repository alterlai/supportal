<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


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
    private $document;

    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="file_name")
     */
    private $file_content;

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

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getFileContent()
    {
        return $this->file_content;
    }

    public function setFileContent($file_content = null): void
    {
        $this->file_content = $file_content;

        if ($file_content) {
            $this->uploaded_at = new \DateTime('now');
        }
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

    public function getFileName(): ?string
    {
        return $this->getDocument()->getFileName();
        // TODO: add extension for draft.
    }
}