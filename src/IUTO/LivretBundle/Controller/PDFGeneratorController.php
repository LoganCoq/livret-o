<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


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
        $tuteurs = $projet->getTuteurs();
        $formation = $etudiants{0}->getFormations(){0}->getTypeFormation();
        $departement = $etudiants{0}->getFormations(){0}->getDepartement()->getNomDpt();

        $template = $this->renderView('::pdf.html.twig',
            ['nom' => $nomP,
                'descrip' => $descripP,
                'bilan' => $bilanP,
                'client' => $clientP,
                'etudiants' => $etudiants,
                'tuteurs' => $tuteurs,
                'formation' => $formation,
                'departement' => $departement,
            ]);


        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->generatePdf($template, "projetPDF");
    }

    public function downloadAction($id)
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
        $tuteurs = $projet->getTuteurs();
        $formation = $etudiants{0}->getFormations(){0}->getTypeFormation();
        $departement = $etudiants{0}->getFormations(){0}->getDepartement()->getNomDpt();

        $template = $this->renderView('::pdf.html.twig',
            ['nom' => $nomP,
                'descrip' => $descripP,
                'bilan' => $bilanP,
                'client' => $clientP,
                'etudiants' => $etudiants,
                'tuteurs' => $tuteurs,
                'formation' => $formation,
                'departement' => $departement,
            ]);


        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->downloadPdf($template, "projetPDF");
    }
}

