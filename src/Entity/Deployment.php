<?php

namespace App\Entity;

use App\Repository\DeploymentRepository;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: DeploymentRepository::class)]
class Deployment
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	private readonly Uuid $id;

	/**
	 * @var Collection<int, Shift>
	 */
	#[ORM\OneToMany(targetEntity: Shift::class, mappedBy: 'deployment', cascade: ['persist','remove'], orphanRemoval: true)]
	private Collection $shift;

	public function __construct(
		
		#[ORM\Column(type: Types::DATE_IMMUTABLE)]
		private DateTimeImmutable $start_date,
		
		#[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
		private ?DateTimeImmutable $end_date = null,
		
		#[ORM\Column(length: 128)]
		private ?string $name = null,

		#[ORM\Column(nullable: true)]
		private ?int $shift_needed = null,
		
		?Uuid $id = null,
	){
		$this->id = $id ?? Uuid::v4();
		
		$this->shift = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function setId(Uuid $id): static
	{
		$this->id = $id;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getStartDate(): DateTimeImmutable
	{
		return $this->start_date;
	}

	public function setStartDate(DateTimeImmutable $start_date): static
	{
		$this->start_date = $start_date;

		return $this;
	}

	public function getEndDate(): ?DateTimeImmutable
	{
		return $this->end_date;
	}

	public function setEndDate(?DateTimeImmutable $end_date): static
	{
		$this->end_date = $end_date;

		return $this;
	}

	public function getShiftNeeded(): ?int
	{
		return $this->shift_needed;
	}

	public function setShiftNeeded(?int $shift_needed): static
	{
		$this->shift_needed = $shift_needed;

		return $this;
	}

	/**
	 * @return Collection<int, Shift>
	 */
	public function getShift(): Collection
	{
		return $this->shift;
	}

	public function addShift(Shift $shift): static
	{
		if (!$this->shift->contains($shift)) {
			$this->shift->add($shift);
			$shift->setDeployment($this);
		}

		return $this;
	}

	public function removeShift(Shift $shift): static
	{
		$this->shift->removeElement($shift);

		return $this;
	}
}
