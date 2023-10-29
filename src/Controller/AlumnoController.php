<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Form\AlumnoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AlumnoController extends AbstractController
{
    #[Route('/registro', name: 'app_alumno')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $entityManager ): Response
    
    {
        $alumno = new Alumno;
        $form = $this->createForm(AlumnoType::class, $alumno);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $plaintextPassword = $form->get('password')->getData();

            $hashedPassword = $userPasswordHasherInterface->hashPassword(
                $alumno,
                $plaintextPassword
            );
            $alumno->setPassword($hashedPassword);
            $alumno->setRoles(['ROLE_USER']);
            
            $entityManager->persist($alumno);

            

            $entityManager->flush();
            return $this->redirectToRoute('app_alumno');}

        return $this->render('alumno/index.html.twig', 
        ['controller_name' => 'AlumnoController',
        'form' => $form->createView(),
    ]);

    }
    #[Route('/alumno/perfil', name: 'app_perfil')]
    public function perfil(): Response
    {

        $alumno = $this->getUser();

        return $this->render('alumno/perfil.html.twig', [
            'alumno' => $alumno,

        ]);
    }
}
