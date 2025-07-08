<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Form\ClassSectionTypeForm;
use App\Entity\SchoolClass;
use App\Entity\ClassSection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


final class ClassSectionController extends AbstractController
{
    #[Route('/subject/{id}/sections', name: 'app_subject_sections')]
    public function manageSections(Request $request, SchoolClass $schoolClass, EntityManagerInterface $em): Response
    {
        $section = new ClassSection();
        $section->setClass($schoolClass);
        $form = $this->createForm(ClassSectionTypeForm::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure students are linked both ways
            foreach ($section->getStudents() as $student) {
                $student->addClassSection($section);
            }
            $em->persist($section);
            $em->flush();
            $this->addFlash('success', 'Section added.');
            return $this->redirectToRoute('app_subject_sections', ['id' => $schoolClass->getId()]);
        }

        return $this->render('class_section/index.html.twig', [
            'controller_name' => 'Section',
            'schoolClass' => $schoolClass,
            'sections' => $schoolClass->getClassSections(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/subject/{id}/sections/{sectionId}/list_of_students', name: 'app_subject_sections_list_of_students')]
    public function listOfStudents(
        #[MapEntity(id: 'id')] SchoolClass $schoolClass,
        #[MapEntity(id: 'sectionId')] ClassSection $section,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $form = $this->createForm(ClassSectionTypeForm::class, $section, [
            'hide_section_name' => true, // Hide section name field in this form
            'section' => $section,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure students are linked both ways
            foreach ($section->getStudents() as $student) {
                $student->addClassSection($section);
            }
            $em->persist($section);
            $em->flush();
            $this->addFlash('success', 'Section added.');
            return $this->redirectToRoute('app_subject_sections', ['id' => $schoolClass->getId()]);
        }

        return $this->render('class_section/student_list.html.twig', [
            'controller_name' => 'Section',
            'schoolClass' => $schoolClass,
            'form' => $form->createView(),
            'section' => $section,
        ]);
    }
}
