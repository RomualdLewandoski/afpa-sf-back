<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DateRepository")
 */
class Date
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $begin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $end;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $votes_up;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $votes_down;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $project_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBegin(): ?string
    {
        return $this->begin;
    }

    public function setBegin(string $begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(string $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getVotesUp(): ?int
    {
        return $this->votes_up;
    }

    public function setVotesUp(int $votes_up): self
    {
        $this->votes_up = $votes_up;

        return $this;
    }

    public function getVotesDown(): ?int
    {
        return $this->votes_down;
    }

    public function setVotesDown(?int $votes_down): self
    {
        $this->votes_down = $votes_down;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getProjectId(): ?int
    {
        return $this->project_id;
    }

    public function setProjectId(int $project_id): self
    {
        $this->project_id = $project_id;

        return $this;
    }
}
