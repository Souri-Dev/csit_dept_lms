<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ClassSection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $studentNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $course = null;

    #[ORM\Column(length: 50)]
    private ?string $section = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $qr = null;

    #[ORM\ManyToMany(targetEntity: ClassSection::class, inversedBy: 'students')]
    #[ORM\JoinTable(name: 'students_class_sections')]
    private Collection $classSections;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentNumber(): ?string
    {
        return $this->studentNumber;
    }

    public function setStudentNumber(string $studentNumber): static
    {
        $this->studentNumber = $studentNumber;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(string $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function getQr(): ?string
    {
        return $this->qr;
    }

    public function setQr(string $qr): static
    {
        $this->qr = $qr;

        return $this;
    }

    public function __construct()
    {
        $this->classSections = new ArrayCollection();
    }

    public function getClassSections(): Collection
    {
        return $this->classSections;
    }

    public function addClassSection(ClassSection $section): static
    {
        if (!$this->classSections->contains($section)) {
            $this->classSections[] = $section;
        }

        return $this;
    }

    public function removeClassSection(ClassSection $section): static
    {
        $this->classSections->removeElement($section);

        return $this;
    }
}
