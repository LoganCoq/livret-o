<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\User;
use IUTO\LivretBundle\Form\UserModifType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use IUTO\LivretBundle\Form\CommentaireCreateType;
use Symfony\Component\HttpFoundation\Request;
use IUTO\LivretBundle\Form\PresentationType;
use IUTO\LivretBundle\Entity\Livret;

class AdministrateurController extends Controller
{
    public function adminHomeAction()
    {
        $idUniv = \phpCAS::getUser();

        // creation de la vue home
        return $this->render('IUTOLivretBundle:Administrateur:adminHome.html.twig', array(
            'statutCAS' => 'administrateur',
            'info' => array('Gérer un utilisateur'),
            'options' => array('Gérer un utilisateur'),
            'routing_info' => array('/admin/users', '#'),
            'routing_options' => array('/admin/users', '#'),
            'routing_statutCAShome' => '/admin',
        ));
    }

    public function adminChooseUserAction()
    {
        $idUniv = \phpCAS::getUser();

        $repUser = $this->getDoctrine()->getRepository(User::class);
        $users = $repUser->findAll();

        $chiefs = array();
        $teachers = array();
        $students = array();
        $employees = array();
        $admins = array();
        $others = array();
        foreach ($users as $curUser) {
            $curRoles = $curUser->getRoles();
            foreach ($curRoles as $role) {
                if ($role == "ROLE_chief") {
                    array_push($chiefs, $curUser);
                } elseif ($role == "ROLE_faculty") {
                    array_push($teachers, $curUser);
                } elseif ($role == "ROLE_student") {
                    array_push($students, $curUser);
                } elseif ($role == "ROLE_employee") {
                    array_push($employees, $curUser);
                } elseif ($role == "ROLE_admin") {
                    array_push($admins, $curUser);
                } else {
                    array_push($others, $curUser);
                }
            }
        }

        return $this->render('IUTOLivretBundle:Administrateur:adminChooseUser.html.twig', array(
            'statutCAS' => 'administrateur',
            'info' => array('Gérer un utilisateur'),
            'routing_info' => array("/admin/users", "#"),
            'routing_statutCAShome' => '/admin',
            'chiefs' => $chiefs,
            'teachers' => $teachers,
            'students' => $students,
            'employees' => $employees,
            'admins' => $admins,
            'others' => $others,
            'users' => $users,
        ));
    }

    public function modifUserAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $idUniv = \phpCAS::getUser();

        $form = $this->createForm(UserModifType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            // affichage d'un message de confirmation que le projet à bien été créer
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur bien modifié.');

            return $this->redirectToRoute('iuto_livret_admin_chooseUser', array());
        }

        return $this->render('IUTOLivretBundle:Administrateur:adminManageUser.html.twig', array(
            'statutCAS' => 'administrateur',
            'info' => array('Gérer un utilisateur'),
            'routing_info' => array("/admin/users", "#"),
            'routing_statutCAShome' => '/admin',
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
