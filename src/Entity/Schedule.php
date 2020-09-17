<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ScheduleRepository::class)
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $day;

    /**
     * @Assert\Time
     * @ORM\Column(type="time")
     */
    private $startMorning;

    /**
     * @Assert\Time
     * @ORM\Column(type="time")
     */
    private $endMorning;

    /**
     * @Assert\Time
     * @ORM\Column(type="time")
     */
    private $startAfternoon;

    /**
     * @Assert\Time
     * @ORM\Column(type="time")
     */
    private $endAfternoon;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $opening;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $meet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getStartMorning(): ?\DateTimeInterface
    {
        return $this->startMorning;
    }

    public function setStartMorning(\DateTimeInterface $startMorning): self
    {
        $this->startMorning = $startMorning;

        return $this;
    }

    public function getEndMorning(): ?\DateTimeInterface
    {
        return $this->endMorning;
    }

    public function setEndMorning(\DateTimeInterface $endMorning): self
    {
        $this->endMorning = $endMorning;

        return $this;
    }

    public function getStartAfternoon(): ?\DateTimeInterface
    {
        return $this->startAfternoon;
    }

    public function setStartAfternoon(\DateTimeInterface $startAfternoon): self
    {
        $this->startAfternoon = $startAfternoon;

        return $this;
    }

    public function getEndAfternoon(): ?\DateTimeInterface
    {
        return $this->endAfternoon;
    }

    public function setEndAfternoon(\DateTimeInterface $endAfternoon): self
    {
        $this->endAfternoon = $endAfternoon;

        return $this;
    }

    public function getOpening(): ?string
    {
        return $this->opening;
    }

    public function setOpening(string $opening): self
    {
        $this->opening = $opening;

        return $this;
    }

    public function getMeet(): ?string
    {
        return $this->meet;
    }

    public function setMeet(string $meet): self
    {
        $this->meet = $meet;

        return $this;
    }
}
