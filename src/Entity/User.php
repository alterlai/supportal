<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\OneToMany(targetEntity="App\Entity\ViewHistory", mappedBy="user_id", orphanRemoval=false)
     */
    private $viewHistories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="users")
     */
    private $organisation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Issue", mappedBy="issued_to", orphanRemoval=true)
     */
    private $issues;

    public function __construct()
    {
        $this->viewHistories = new ArrayCollection();
        $this->issues = new ArrayCollection();
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

    /**
     * @return Collection|ViewHistory[]
     */
    public function getViewHistories(): Collection
    {
        return $this->viewHistories;
    }

    public function addViewHistory(ViewHistory $viewHistory): self
    {
        if (!$this->viewHistories->contains($viewHistory)) {
            $this->viewHistories[] = $viewHistory;
            $viewHistory->setUserId($this);
        }

        return $this;
    }

    public function removeViewHistory(ViewHistory $viewHistory): self
    {
        if ($this->viewHistories->contains($viewHistory)) {
            $this->viewHistories->removeElement($viewHistory);
            // set the owning side to null (unless already changed)
            if ($viewHistory->getUserId() === $this) {
                $viewHistory->setUserId(null);
            }
        }

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
}
