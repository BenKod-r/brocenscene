<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string", length=255)
     */
    private $day;

    /**
     * @ORM\Column(type="time")
     */
    private $startMorning;

    /**
     * @ORM\Column(type="time")
     */
    private $endMorning;

    /**
     * @ORM\Column(type="time")
     */
    private $startAfternoon;

    /**
     * @ORM\Column(type="time")
     */
    private $endAfternoon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $open;

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

    public function getOpen(): ?string
    {
        return $this->open;
    }

    public function setOpen(string $open): self
    {
        $this->open = $open;

        return $this;
    }
}
