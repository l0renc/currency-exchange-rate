<?php

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
class CurrencyRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $baseCurrency = null;

    #[ORM\Column(length: 50)]
    private ?string $targetCurrency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?float $rate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseCurrency(): ?string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): static
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    public function getTargetCurrency(): ?string
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(string $targetCurrency): static
    {
        $this->targetCurrency = $targetCurrency;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate($rate): self
    {
        $this->rate = $rate;
        return $this;
    }
}
