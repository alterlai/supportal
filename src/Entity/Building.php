<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\organisation", inversedBy="buildings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Floor", mappedBy="building_id", orphanRemoval=true)
     */
    private $floors;

    public function __construct()
    {
        $this->floors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganisationId(): ?organisation
    {
        return $this->organisation;
    }

    public function setOrganisationId(?organisation $organisation_id): self
    {
        $this->organisation_id = $organisation_id;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|Floor[]
     */
    public function getFloors(): Collection
    {
        return $this->floors;
    }

    public function addFloor(Floor $floor): self
    {
        if (!$this->floors->contains($floor)) {
            $this->floors[] = $floor;
            $floor->setBuildingId($this);
        }

        return $this;
    }

    public function removeFloor(Floor $floor): self
    {
        if ($this->floors->contains($floor)) {
            $this->floors->removeElement($floor);
            // set the owning side to null (unless already changed)
            if ($floor->getBuildingId() === $this) {
                $floor->setBuildingId(null);
            }
        }

        return $this;
    }
}
