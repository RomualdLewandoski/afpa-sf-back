<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $cafe;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cgu;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cgv;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rgpd;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCafe(): ?int
    {
        return $this->cafe;
    }

    public function setCafe(int $cafe): self
    {
        $this->cafe = $cafe;

        return $this;
    }

    public function getCgu(): ?string
    {
        return $this->cgu;
    }

    public function setCgu(?string $cgu): self
    {
        $this->cgu = $cgu;

        return $this;
    }

    public function getCgv(): ?string
    {
        return $this->cgv;
    }

    public function setCgv(?string $cgv): self
    {
        $this->cgv = $cgv;

        return $this;
    }

    public function getRgpd(): ?string
    {
        return $this->rgpd;
    }

    public function setRgpd(?string $rgpd): self
    {
        $this->rgpd = $rgpd;

        return $this;
    }
}
