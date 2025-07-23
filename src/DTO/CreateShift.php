<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateShift
{
    public function __construct(
        #[Groups(['write'])]
        #[Assert\NotBlank()]
        #[Assert\Uuid(message: 'Invalid UUID format for deployment_id')]
        public ?string $deployment_id = null,

        #[Groups(['write'])]
        #[Assert\NotBlank()]
        #[Assert\DateTime(format: 'Y-m-d H:i:s', message: 'Date must be in format YYYY-MM-DD HH:MM:SS')]
        public ?string $date = null,

        #[Groups(['write'])]
        #[Assert\NotBlank()]
        #[Assert\Positive(message: 'Worked hours must be greater than zero')]
        public ?float $worked = null,

        #[Groups(['write'])]
        #[Assert\PositiveOrZero()]
        public ?float $pay = null,
    ) { }
}
