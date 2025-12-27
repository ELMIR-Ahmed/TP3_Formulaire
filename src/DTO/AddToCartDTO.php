<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AddToCartDTO
{
    #[Assert\NotBlank(message: 'Please select a quantity')]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'Quantity must be between {{ min }} and {{ max }}'
    )]
    private ?int $quantity = 1;

    #[Assert\NotBlank(message: 'Please select a color')]
    #[Assert\Choice(
        choices: ['black', 'white', 'silver'],
        message: 'Please select a valid color'
    )]
    private ?string $color = null;

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }
}
