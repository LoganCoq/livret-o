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
//        $descripP = $projet->getDescripProjet();
        $descripP = preg_replace("/\r\n/","\ ",$projet->getDescripProjet());
        $bilanP = $projet->getBilanProjet();
        $clientP = $projet->getClientProjet();
        $descripCli = $projet->getDescriptionClientProjet();
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
                'descripCli' => $descripCli,
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
//        $descripP = preg_replace("/\r\n|\r|\n/","<br/>\n",$descripP);
        $bilanP = $projet->getBilanProjet();
        $clientP = $projet->getClientProjet();
        $descripCli = $projet->getDescriptionClientProjet();
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
                'descripCli' => $descripCli,
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

        $template = $this->renderView('::edito.html.twig',
            ['texte' => $livret->getEditoLivret(),
            ]);

        $html2pdf->write($template);

        $projets = $livret->getProjets();

        foreach ( $projets as $projet)
        {

            $nomP = $projet->getIntituleProjet();
            $descripP = $projet->getDescripProjet();
            $bilanP = $projet->getBilanProjet();
            $clientP = $projet->getClientProjet();
            $descripCli = $projet->getDescriptionClientProjet();
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
                    'descripCli' => $descripCli,
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

    public function downloadLivretAction($id)
    {
        $repLivret = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Livret');

        $repProjet = $this->getDoctrine()->getManager()->getRepository('IUTOLivretBundle:Projet');

        $livret = $repLivret->findOneById($id);

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        $template = $this->renderView('::edito.html.twig',
            ['texte' => $livret->getEditoLivret(),
            ]);

        $html2pdf->write($template);

        $projets = $livret->getProjets();

        foreach ( $projets as $projet)
        {

            $nomP = $projet->getIntituleProjet();
            $descripP = $projet->getDescripProjet();
            $bilanP = $projet->getBilanProjet();
            $clientP = $projet->getClientProjet();
            $descripCli = $projet->getDescriptionClientProjet();
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
                    'descripCli' => $descripCli,
                    'etudiants' => $etudiants,
                    'tuteurs' => $tuteurs,
                    'formation' => $formation,
                    'departement' => $departement,
                    'image1' => $image1,
                    'image2' => $image2,
                ]);

            $html2pdf->write($template);
        }

        $html2pdf->getDownloadPdf("livret");
    }

    function smart_wordwrap($string, $width = 70, $break = "<br>") {
// split on problem words over the line length
        $pattern = sprintf('/([^ ]{%d,})/', $width);
        $output = '';
        $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        foreach ($words as $word) {
            // normal behaviour, rebuild the string
            if (false !== strpos($word, ' ')) {
                $output .= $word;
            } else {
                // work out how many characters would be on the current line
                $wrapped = explode($break, wordwrap($output, $width, $break));
                $count = $width - (strlen(end($wrapped)) % $width);

                // fill the current line and add a break
                $output .= substr($word, 0, $count) . $break;

                // wrap any remaining characters from the problem word
                $output .= wordwrap(substr($word, $count), $width, $break, true);
            }
        }

        // wrap the final output
        return wordwrap($output, $width, $break);
    }
}

