<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Location;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuildingRepository")
 */
class Building
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="buildings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Area", mappedBy="building", orphanRemoval=true)
     */
    private $areas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="building")
     */
    private $documents;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    public function __construct()
    {
        $this->areas = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    /**
     * This returns the full name of the location and the corresponding organisation name
     * This is used to differentiate between two locations with the same name.
     */
    public function getFullName()
    {
        /** @var Organisation $organisation */
        return $this->getLocation()->getOrganisation()->getName() . " : " . $this->getName();
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

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
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
            $document->setBuilding($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getBuilding() === $this) {
                $document->setBuilding(null);
            }
        }

        return $this;
    }
    
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function addArea(Area $area): self
    {
        if (!$this->areas->contains($area)) {
            $this->areas[] = $area;
            $area->setBuilding($this);
        }

        return $this;
    }

    public function removeArea(Area $area): self
    {
        if ($this->areas->contains($area)) {
            $this->areas->removeElement($area);
            // set the owning side to null (unless already changed)
            if ($area->getBuilding() === $this) {
                $area->setBuilding(null);
            }
        }

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
}
