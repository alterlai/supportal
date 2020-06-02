<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DraftStatusRepository")
 */
class DraftStatus
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DocumentDraft", mappedBy="draftStatus")
     */
    private $draft;

    public function __construct()
    {
        $this->draft = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|DocumentDraft[]
     */
    public function getDraft(): Collection
    {
        return $this->draft;
    }

    public function addDraft(DocumentDraft $draft): self
    {
        if (!$this->draft->contains($draft)) {
            $this->draft[] = $draft;
            $draft->setDraftStatus($this);
        }

        return $this;
    }

    public function removeDraft(DocumentDraft $draft): self
    {
        if ($this->draft->contains($draft)) {
            $this->draft->removeElement($draft);
            // set the owning side to null (unless already changed)
            if ($draft->getDraftStatus() === $this) {
                $draft->setDraftStatus(null);
            }
        }

        return $this;
    }
}
