<?php

namespace App\Entity;

use App\Repository\ClassSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Student;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassSectionRepository::class)]
class ClassSection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $sectionName = null;

    #[ORM\ManyToOne(inversedBy: 'classSections')]
    private ?SchoolClass $class = null;

    #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'classSections')]
    private Collection $students;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSectionName(): ?string
    {
        return $this->sectionName;
    }

    public function setSectionName(string $sectionName): static
    {
        $this->sectionName = $sectionName;

        return $this;
    }

    public function getClass(): ?SchoolClass
    {
        return $this->class;
    }

    public function setClass(?SchoolClass $class): static
    {
        $this->class = $class;

        return $this;
    }

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->addClassSection($this); // keep the relation bidirectional
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            $student->removeClassSection($this);
        }

        return $this;
    }
}
