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
            'routing_info' => array('/'.$id.'/correctionProf1', '#'),
            'routing_options' => array('/'.$id.'/correctionProf1', '#'),
            'names' => $names, '#'));
    }

    public function correctionTeacher1Action($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:User');
        $projets = $repository->findOneById($id)->getProjetSuivis();
        $nomProjets = array();
        foreach($projets as $elem){
            array_push($nomProjets, $elem->getIntituleProjet());
        };



        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher1.html.twig', array('id' => $id,
            'statutCAS' => 'professeur',
            'projets' => $nomProjets,
            'routing_statutCAShome' => '/'.$id.'/professeur',
            'info' => array('Demandes de correction', 'Projets validés'),
            'routing_info' => array('/'.$id.'/correctionProf1', '#')));

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

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $commentaires = $repository->findOneByProjet($idProjet);

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher2.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_statutCAShome' => '/'.$id.'/professeur',
                'commentaires' => $commentaires,
                'routing_info' => array('/'.$id.'/correctionProf1', '#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => array('/'.$id.'/correctionProf1'),
                'pageSuiv' => array('/'.$id.'/'.$idProjet.'/correctionProf3')
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

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Commentaire');
        $commentaires = $repository->findOneByProjet($idProjet);
        $idProjet = $projet->getId();

        return $this->render('IUTOLivretBundle:Teacher:correctionTeacher3.html.twig',
            array('form' => $form->createView(),
                'statutCAS' => 'professeur',
                'commentaires' => $commentaires,
                'routing_statutCAShome' => '/'.$id.'/professeur',
                'info' => array('Demandes de correction', 'Projets validés'),
                'routing_info' => array('/'.$id.'/correctionProf1', '#'),
                'routing_options' => array('#', '#'),
                'pagePrec' => array('/'.$id.'/'.$idProjet.'/correctionProf2'),
                'pageSuiv' => array('/'.$id.'/correctionProf1')
            ));
    }
}
