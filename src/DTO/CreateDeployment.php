<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDeployment
{
    public function __construct(
        #[Groups(['write'])]
        #[Assert\NotBlank(groups: ['write'])]
        #[Assert\Uuid(strict: true)]
        public ?string $id = null,

        #[Groups(['write'])]
        #[Assert\NotBlank(groups: ['write'])]
        #[Assert\Length(min: 3, max: 128)]
        public ?string $name = null,

        #[Groups(['write'])]
        #[Assert\NotBlank(groups: ['write'])]
        #[Assert\Date()]
        public ?string $start_date = null,

        #[Groups(['write'])]
        #[Assert\Date()]
        public ?string $end_date = null,

        #[Groups(['write'])]
        #[Assert\Range(min: 1, max: 999)]
        public ?int $shift_needed = null,
    ) {}
}
