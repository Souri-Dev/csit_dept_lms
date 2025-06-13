<?php

namespace App\Controller;

use App\Repository\SchoolClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\SchoolClassTypeForm;
use App\Entity\SchoolClass as SchoolClass;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class SubjectController extends AbstractController
{
    #[Route('/subject', name: 'app_subject')]
    public function index(Request $request, EntityManagerInterface $em, SchoolClassRepository $schoolclassRepository): Response
    {
        $schoolClasses = $schoolclassRepository->findAll();

        $schoolClass = new SchoolClass();
        $form = $this->createForm(SchoolClassTypeForm::class, $schoolClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($schoolClass);
            $em->flush();

            // redirect or render as needed
            return $this->redirectToRoute('app_subject', [
                // 'id' => $student->getId(),
            ]);
        }
        return $this->render('subject/index.html.twig', [
            'controller_name' => 'SubjectController',
            'form' => $form->createView(),
            'schoolClasses' => $schoolClasses,
        ]);
    }
}
