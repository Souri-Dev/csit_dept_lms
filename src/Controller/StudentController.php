<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Form\StudentTypeForm as StudentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



final class StudentController extends AbstractController
{

    #[Route('/student', name: 'app_student_index')]
    public function index(Request $request, EntityManagerInterface $em, StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAll();

        // Create an empty form for the modal
        $student = new Student();
        // $form = $this->createForm(StudentType::class, $studentEntity);
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Generate a UUID string for QR field
            $student->setQr(Uuid::v4()->toRfc4122());

            $em->persist($student);
            $em->flush();

            // Redirect to a show page (optional)
            return $this->redirectToRoute('app_student_index', [
                // 'id' => $student->getId(),
            ]);
        }

        return $this->render('student/index.html.twig', [
            'students' => $students,
            'form' => $form->createView(),
        ]);

        $qrValue = $student->getQr() ?: 'no-qr-value';

        $qrCode = new QrCode($qrValue);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return new Response(
            $result->getString(),
            Response::HTTP_OK,
            ['Content-Type' => $result->getMimeType()]
        );
    }

    // #[Route('/student/new', name: 'app_student_new')]
    // public function new(Request $request, EntityManagerInterface $em): Response
    // {
    //     $student = new Student();

    //     $form = $this->createForm(StudentType::class, $student);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Generate a UUID string for QR field
    //         $student->setQr(Uuid::v4()->toRfc4122());

    //         $em->persist($student);
    //         $em->flush();

    //         // Redirect to a show page (optional)
    //         return $this->redirectToRoute('app_student_show', [
    //             'id' => $student->getId(),
    //         ]);
    //     }

    //     return $this->render('student/new.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    #[Route('/student/{id}/json', name: 'app_student_json', methods: ['GET'])]
    public function getStudentJson(Student $student): Response
    {
        $qrUrl = $this->generateUrl('app_student_qr', ['id' => $student->getId()], UrlGeneratorInterface::ABSOLUTE_URL);


        return $this->json([
            'id' => $student->getId(),
            'name' => $student->getName(),
            'course' => $student->getCourse(),
            'section' => $student->getSection(),
            'studentNumber' => $student->getStudentNumber(),
            'qr' => $qrUrl,
        ]);
    }




    #[Route('/student/{id}/qr', name: 'app_student_qr')]
    public function generateQr(Student $student): Response
    {
        $qrValue = $student->getQr() ?: 'no-qr-value';

        $qrCode = new QrCode($qrValue);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return new Response(
            $result->getString(),
            Response::HTTP_OK,
            ['Content-Type' => $result->getMimeType()]
        );
    }




    #[Route('/student/{id}', name: 'app_student_show')]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }
}
