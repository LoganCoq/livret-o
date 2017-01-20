<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use IUTO\LivretBundle\Entity\Projet;


class PDFGeneratorController extends Controller
{
    public function generatorAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Projet');
        $projet = $repository->findOneById($id);


        $nomP = $projet->getIntituleProjet();
        $descripP = $projet->getDescripProjet();
        $bilanP = $projet->getBilanProjet();
        $clientP = $projet->getClientProjet();
        $etudiants = $projet->getEtudiants();
        $tuteurs = $projet->getPersonnels();


        $template = $this->renderView('::pdf.html.twig',
            ['nom' => $nomP,
                'descrip' => $descripP,
                'bilan' => $bilanP,
                'client' => $clientP,
                'etudiants' => $etudiants,
                'tuteurs' => $tuteurs
            ]);

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->generatePdf($template, "projetPDF");
    }
}

