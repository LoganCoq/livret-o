<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IUTO\LivretBundle\Entity\Projet;
use IUTO\LivretBundle\Form\ProjetModifType;
use IUTO\LivretBundle\Form\ProjetContenuType;
use Symfony\Component\HttpFoundation\Request;


class TeacherController extends Controller
{
    public function teacherhomeAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $names = $repository->findOneById($id)->getUsername();
        return $this->render('IUTOLivretBundle:Teacher:teacherhome.html.twig', array('statutCAS' => 'professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'options' => array('Voir les demande de correction de projets', 'Voir les projets validés'),
            'routing_statutCAShome' => '/'.$id.'/professeur',
            'routing_info' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
            'routing_options' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
            'names' => $names, '#'));
    }

    public function correctionTeacher1Action($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();


        $projetsValides = array();
        foreach($projets as $elem){
            if ($elem->getValiderProjet() == 1)
            array_push($projetsValides, $elem);
        };

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher1.html.twig', array(
            'id' => $id,
            'statutCAS' => 'professeur',
            'projets' => $projetsValides,
            'routing_statutCAShome' => '/'.$id.'/professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'routing_info' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
            'pagePrec' => '/'.$id.'/professeur',
        ));

    }

    public function correctionTeacher2Action(Request $request, $id, Projet $projet)
    {

        $form = $this->createForm(ProjetModifType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }
        $idProjet = $projet->getId();


        $repository2 = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository2->findByProjet($projet);

        $commentaires = array();
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getPrenomUser()." ".$user->getNomUser());
            array_push($x, $elem->getContenu());
            array_push($x, $elem->getDate());
            array_push($x, $user->getRole());
            array_push($commentaires, $x);

        };

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_statutCAShome' => '/'.$id.'/professeur',
                'commentaires' => $commentaires,
                'routing_info' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/'.$id.'/correctionProf1',
                'pageSuiv' => '/'.$id.'/'.$idProjet.'/correctionProf3'
            ));
    }

    public function correctionTeacher3Action(Request $request, $id, Projet $projet)
    {
        $form = $this->createForm(ProjetContenuType::class, $projet);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        $idProjet = $projet->getId();

        $repository2 = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $com = $repository2->findByProjet($projet);

        $commentaires = array();
        foreach($com as $elem){
            $x=array();
            $user = $elem->getUser();
            array_push($x, $user->getRole());
            array_push($x, $elem->getContenu());
            array_push($commentaires, $x);

        };

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'professeur',
                'commentaires' => $commentaires,
                'routing_statutCAShome' => '/'.$id.'/professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_info' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
                'routing_options' => array('#', '#'),
                'pagePrec' => '/'.$id.'/'.$idProjet.'/correctionProf2',
                'pageSuiv' => '/'.$id.'/'.$idProjet.'/correctionProf4'
            ));
    }

    public function correctionTeacher4Action(Request $request, $id, Projet $projet)
    {
        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher4.html.twig',
            array('id' => $id,
                'statutCAS' => 'professeur',
                'routing_statutCAShome' => '/'.$id.'/professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_info' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
                'pagePrec' => '/'.$id.'/'.$idProjet.'/correctionProf3'
                ));
    }

    public function projetsValidesTeacher1Action(Request $request, $id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();



        $projetsValides = array();
        foreach($projets as $elem){
            if ($elem->getValiderProjet() == 0)
                array_push($projetsValides, $elem);
        };

        return $this->render('IUTOLivretBundle:Teacher:projetsValidesTeacher1.html.twig', array(
            'id' => $id,
            'statutCAS' => 'professeur',
            'projets' => $projetsValides,
            'routing_statutCAShome' => '/'.$id.'/professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'routing_info' => array('/'.$id.'/correctionProf1', '/'.$id.'/projetsValides1'),
            'pagePrec' => '/'.$id.'/professeur',
        ));

    }

}
