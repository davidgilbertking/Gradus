<?php

namespace App\Entity;

use App\Repository\ShiftRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Types\UuidType;

use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: ShiftRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Shift
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	private readonly Uuid $id;

	#[ORM\ManyToOne(inversedBy: 'shift')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Deployment $deployment = null;


    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updated_at;

	public function __construct(
		Deployment $deployment,

		#[ORM\Column]
		private DateTimeImmutable $date,

		#[ORM\Column]
		private float $worked,

		#[ORM\Column(nullable: true, type: 'decimal', precision: 10, scale: 2)]
		private ?float $pay = null,

		?Uuid $id = null,
	){
		$this->id = $id ?? Uuid::v4();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();

        $deployment->addShift($this);
	}

	public function getId(): Uuid
	{
		return $this->id;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function setDate(DateTimeImmutable $date): static
	{
		$this->date = $date;

		return $this;
	}

	public function getPay(): ?float
	{
		return $this->pay;
	}

	public function setPay(?float $pay): static
	{
		$this->pay = $pay;

		return $this;
	}

	public function getWorked(): float
	{
		return $this->worked;
	}

	public function setWorked(float $worked): static
	{
		$this->worked = $worked;

		return $this;
	}

	public function getDeployment(): Deployment
	{
		return $this->deployment;
	}

	public function setDeployment(Deployment $deployment): static
	{
		$this->deployment = $deployment;

		return $this;
	}

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }
}
