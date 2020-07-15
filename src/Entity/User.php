<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="Een gebruiker met deze username bestaat al.")
 * @UniqueEntity(fields="email", message="Een gebruiker met dit email adres bestaat al.")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="users")
     */
    private $organisation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Issue", mappedBy="issued_to", orphanRemoval=true)
     */
    private $issues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DocumentDraft", mappedBy="uploaded_by", orphanRemoval=true)
     */
    private $documentDrafts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserAction", mappedBy="user")
     */
    private $userActions;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $suspended;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
        $this->documentDrafts = new ArrayCollection();
        $this->userActions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return [$this->getRole()];
    }

    public function setRoles(array $roles): self
    {
        $this->role = $roles[0];

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        if (!$password) return $this; // if password is empty, just return

        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOrganisation(): ?organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return Collection|Issue[]
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function getIssueCount(): int
    {
        return sizeof($this->issues);
    }

    public function addIssue(Issue $issue): self
    {
        if (!$this->issues->contains($issue)) {
            $this->issues[] = $issue;
            $issue->setIssuedTo($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): self
    {
        if ($this->issues->contains($issue)) {
            $this->issues->removeElement($issue);
            // set the owning side to null (unless already changed)
            if ($issue->getIssuedTo() === $this) {
                $issue->setIssuedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DocumentDraft[]
     */
    public function getDocumentDrafts(): Collection
    {
        return $this->documentDrafts;
    }

    public function addDocumentDraft(DocumentDraft $documentDraft): self
    {
        if (!$this->documentDrafts->contains($documentDraft)) {
            $this->documentDrafts[] = $documentDraft;
            $documentDraft->setUploadedBy($this);
        }

        return $this;
    }

    public function removeDocumentDraft(DocumentDraft $documentDraft): self
    {
        if ($this->documentDrafts->contains($documentDraft)) {
            $this->documentDrafts->removeElement($documentDraft);
            // set the owning side to null (unless already changed)
            if ($documentDraft->getUploadedBy() === $this) {
                $documentDraft->setUploadedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserAction[]
     */
    public function getUserActions(): Collection
    {
        return $this->userActions;
    }

    public function addUserAction(UserAction $userAction): self
    {
        if (!$this->userActions->contains($userAction)) {
            $this->userActions[] = $userAction;
            $userAction->setUser($this);
        }

        return $this;
    }

    public function removeUserAction(UserAction $userAction): self
    {
        if ($this->userActions->contains($userAction)) {
            $this->userActions->removeElement($userAction);
            // set the owning side to null (unless already changed)
            if ($userAction->getUser() === $this) {
                $userAction->setUser(null);
            }
        }

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function isSuspended(): ?bool
    {
        return $this->suspended;
    }

    public function setSuspended(bool $suspended): self
    {
        $this->suspended = $suspended;

        return $this;
    }
}
