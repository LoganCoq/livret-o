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

        if ( $projet->getImages()->count() == 2 )
        {
            $image1 = $projet->getImages(){0};
            $image2 = $projet->getImages(){1};
        }
        elseif ( $projet->getImages()->count() == 1 )
        {
            $image1 = $projet->getImages(){0};
            $image2 = null;
        }
        else{
            $image1 = null;
            $image2 = null;
        }

        $template = $this->renderView('::pdf.html.twig',
            ['nom' => $nomP,
                'descrip' => $descripP,
                'bilan' => $bilanP,
                'client' => $clientP,
                'etudiants' => $etudiants,
                'tuteurs' => $tuteurs,
                'formation' => $formation,
                'departement' => $departement,
                'image1' => $image1,
                'image2' => $image2,
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

        if ( $projet->getImages()->count() == 2 )
        {
            $image1 = $projet->getImages(){0};
            $image2 = $projet->getImages(){1};
        }
        elseif ( $projet->getImages()->count() == 1 )
        {
            $image1 = $projet->getImages(){0};
            $image2 = null;
        }
        else{
            $image1 = null;
            $image2 = null;
        }

        $template = $this->renderView('::pdf.html.twig',
            ['nom' => $nomP,
                'descrip' => $descripP,
                'bilan' => $bilanP,
                'client' => $clientP,
                'etudiants' => $etudiants,
                'tuteurs' => $tuteurs,
                'formation' => $formation,
                'departement' => $departement,
                'image1' => $image1,
                'image2' => $image2,
            ]);


        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->downloadPdf($template, "projetPDF");
    }

    public function generatorManyAction($idLivret)
    {
        $repLivret = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Livret');

        $repProjet = $this->getDoctrine()->getManager()->getRepository('IUTOLivretBundle:Projet');

        $livret = $repLivret->findOneById($idLivret);

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        $projets = $livret->getProjets();

        foreach ( $projets as $projet)
        {

            $nomP = $projet->getIntituleProjet();
            $descripP = $projet->getDescripProjet();
            $bilanP = $projet->getBilanProjet();
            $clientP = $projet->getClientProjet();
            $etudiants = $projet->getEtudiants();
            $tuteurs = $projet->getTuteurs();
            $formation = $etudiants{0}->getFormations(){0}->getTypeFormation();
            $departement = $etudiants{0}->getFormations(){0}->getDepartement()->getNomDpt();

            if ( $projet->getImages()->count() == 2 )
            {
                $image1 = $projet->getImages(){0};
                $image2 = $projet->getImages(){1};
            }
            elseif ( $projet->getImages()->count() == 1 )
            {
                $image1 = $projet->getImages(){0};
                $image2 = null;
            }
            else{
                $image1 = null;
                $image2 = null;
            }

            $template = $this->renderView('::pdf.html.twig',
                ['nom' => $nomP,
                    'descrip' => $descripP,
                    'bilan' => $bilanP,
                    'client' => $clientP,
                    'etudiants' => $etudiants,
                    'tuteurs' => $tuteurs,
                    'formation' => $formation,
                    'departement' => $departement,
                    'image1' => $image1,
                    'image2' => $image2,
                ]);

	        $html2pdf->write($template);
        }

        $html2pdf->getOutputPdf("livret");
    }
}

