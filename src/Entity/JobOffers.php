<?php

namespace App\Entity;

use App\Repository\JobOffersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOffersRepository::class)]
class JobOffers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $zipcode = null;

    /**
     * Many Groups have Many Users.
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'jobOffers')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'jobOffer')]
    private ?Recruiter $recruiter = null;

    #[ORM\ManyToMany(targetEntity: Candidate::class, inversedBy: 'offers')]
    #[ORM\JoinTable(name:"candidate_job_offers")]
    private Collection $candidates;

    #[ORM\Column]
    private ?bool $validationJob = null;

    #[ORM\Column]
    private ?bool $consultanValidate = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->candidates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): static
    {
        $this->recruiter = $recruiter;

        return $this;
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): void
    {
        $this->candidates[] = $candidate;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            $candidate->removeApply($this);
        }

        return $this;
    }

    public function isValidationJob(): ?bool
    {
        return $this->validationJob;
    }

    public function setValidationJob(bool $validationJob): static
    {
        $this->validationJob = $validationJob;

        return $this;
    }

    public function isConsultanValidate(): ?bool
    {
        return $this->consultanValidate;
    }

    public function setConsultanValidate(bool $consultanValidate): static
    {
        $this->consultanValidate = $consultanValidate;

        return $this;
    }


}