<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="location")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Building", mappedBy="location", orphanRemoval=false)
     */
    private $buildings;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->buildings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
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

    /**
     * This returns the full name of the location and the corresponding organisation name
     * This is used to differentiate between two locations with the same name.
     */
    public function getFullName()
    {
        /** @var Organisation $organisation */
        return $this->getOrganisation()->getName() . " : " . $this->getName();
    }

    /**
     * This is only a stub because easyAdmin requires a setter and getter.
     * This function only calls the setName function
     * @param string $name
     * @return Location
     */
    public function setFullName(string $name): self
    {
        return $this->setName($name);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setLocation($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getLocation() === $this) {
                $document->setLocation(null);
            }
        }

        return $this;
    }

    public function getBuildings()
    {
        return $this->buildings;
    }

    public function addBuilding(Building $building)
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings[] = $building;
            $building->setLocation($this);
        }

        return $this;
    }

    public function removeBuildings(Building $building): self
    {
        if ($this->buildings->contains($building)) {
            $this->buildings->removeElement($building);
            // set the owning side to null (unless already changed)
            if ($building->getLocation() === $this) {
                $building->setLocation(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
