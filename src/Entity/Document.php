<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @Vich\Uploadable
 */
class Document
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @var string
     */
    private $file_name;

    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="file_name")
     */
    private $file_content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discipline", inversedBy="documents", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $discipline;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Area", inversedBy="documents")
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Building", inversedBy="documents")
     */
    private $building;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="documents")
     */
    private $location;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $floor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DocumentHistory", mappedBy="document")
     */
    private $documentHistories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DocumentType", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $documentType;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Issue", mappedBy="document", cascade={"persist", "remove"})
     */
    private $issue;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $version;


    public function __construct()
    {
        $this->documentHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    /**
     * Return filename without file extension
     * @return string
     */
    public function getDocumentName(): string
    {
        return substr($this->file_name, 0, -4);
    }

    public function setFileName(?string $file_name): self
    {
        $this->file_name = $file_name;

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
            $this->updated_at = new \DateTime('now');
        }
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(?int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getDisciplineCode(): ?discipline
    {
        return $this->discipline;
    }

    public function setDisciplineCode(?discipline $discipline_code): self
    {
        $this->discipline = $discipline_code;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

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

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    /** Files only contain .pdf or .dwg, so slice last four characters */
    public function getFileType()
    {
        return substr($this->file_name, -4, 4);
    }

    public function __toString()
    {
        return $this->getFileName();
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

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

    /**
     * @return Collection|DocumentHistory[]
     */
    public function getDocumentHistories(): Collection
    {
        return $this->documentHistories;
    }

    public function addDocumentHistory(DocumentHistory $documentHistory): self
    {
        if (!$this->documentHistories->contains($documentHistory)) {
            $this->documentHistories[] = $documentHistory;
            $documentHistory->setDocument($this);
        }

        return $this;
    }

    public function removeDocumentHistory(DocumentHistory $documentHistory): self
    {
        if ($this->documentHistories->contains($documentHistory)) {
            $this->documentHistories->removeElement($documentHistory);
            // set the owning side to null (unless already changed)
            if ($documentHistory->getDocument() === $this) {
                $documentHistory->setDocument(null);
            }
        }

        return $this;
    }

    public function getDocumentType(): ?DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(?DocumentType $documentType): self
    {
        $this->documentType = $documentType;

        return $this;
    }

    public function getIssue(): ?Issue
    {
        return $this->issue;
    }

    public function setIssue(Issue $issue): self
    {
        $this->issue = $issue;

        // set the owning side of the relation if necessary
        if ($issue->getDocument() !== $this) {
            $issue->setDocument($this);
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param integer $version
     */
    public function setVersion($version): void
    {
        $this->version = $version;
    }
}
