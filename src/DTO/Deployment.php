<?php

namespace App\DTO\Deployment;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Groups;

class Deployment
{
	public function __construct(
		#[Groups(['write'])]
		#[Assert\NotBlank(groups: ['write'])]
		#[Assert\Uuid(strict: true)]
		public ?string $id = null,
		
		#[Groups(['read', 'write'])]
		#[Assert\NotBlank(groups: ['write'])]
		#[Assert\Length(
			min: 3, max: 128,
			notInRangeMessage: 'Name field must be between {{ min }} and {{ max }} characters long. Provided name is {{ value_length }} characters long.'
		)]
		public ?string $name = null,
		
		#[Groups(['read', 'write'])]
		#[Assert\NotBlank(groups: ['write'])]
		#[Assert\Date(message: 'Start date must be in format YYYY-MM-DD')]
		public ?string $start_date = null,
		
		#[Groups(['read', 'write'])]
		#[Assert\GreaterThanOrEqual(
			propertyPath: 'start_date',
			message: 'End date must be greater than or equal to start date: {{ compared_value }}. Your end date is {{ value }}.'
		)]
		#[Assert\Date(message: 'End date must be in format YYYY-MM-DD')]
		public ?string $end_date = null,
		
		#[Assert\Range(
			min: 1, max: 999,
			notInRangeMessage: "This number of shifts is not supported. Awaiting in range {{ min }} - {{ max }}"
		)]
		#[Groups(['read', 'write'])]
		public ?int $shift_needed = null,
	) { }
}