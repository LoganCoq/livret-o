<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class PDFGeneratorController extends Controller
{
    /**
     * @param $id
     * @return mixed
     *
     *  Controlleur gérant la génération et l'affichage d'un projet
     *  au format pdf
     */
    public function generatorAction($id)
    {
//        Récupération du repository des projets
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Projet');

//        Récupération du projet que l'on veut générer
        $projet = $repository->findOneById($id);

//        Récupération des informations sur le projet
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

//            Récupération des images du projet, on met à null si il n'y a pas d'image
//            afin d'éviter les erreurs
        $images = $projet->getImages();
        $logo = null;
        $image1 = null;
        $image2 = null;
        if ( $images->count() == 3 ) //TODO cleaner
        {
            if ($images{0}->getIsLogo()){
                $logo = $images{0};
            } else {
                $image1 = $images{0};
            }
            if ($images{1}->getIsLogo()){
                $logo = $images{1};
            } else {
                if ($image1 != null)
                {
                    $image1 = $images{1};
                } else {
                    $image2 = $images{1};
                }
            }
            if ($images{2}->getIsLogo()){
                $logo = $images{2};
            } else {
                $image2 = $images{2};
            }
        }
        elseif ( $images->count() == 2 )
        {
            if ($images{0}->getIsLogo()){
                $logo = $images{0};
            } else {
                $image1 = $images{0};
            }
            if ($images{1}->getIsLogo()){
                $logo = $images{1};
            } else {
                if ($image1 != null)
                {
                    $image1 = $images{1};
                } else {
                    $image2 = $images{1};
                }
            }
        } elseif ( $images->count() == 1){
            if ($images{0}->getIsLogo()){
                $logo = $images{0};
            } else {
                $image1 = $images{0};
            }
        }

//        Création du template du projet avec les informations de celui-ci
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
                'logo' => $logo,
            ]);

//        Récupération de l'application de génération de pdf
        $html2pdf = $this->get('app.html2pdf');
//        Création d'un pdf
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

//        Génération du pdf et affichage
        return $html2pdf->generatePdf($template, "projetPDF");
    }

    /**
     * @param $id
     * @return mixed
     *
     *  Controlleur gérant la génération et le téléchargement d'un projet
     *  au format pdf
     */
    public function downloadAction($id)
    {
        //        Récupération du repository des projets
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Projet');

//        Récupération du projet que l'on veut générer
        $projet = $repository->findOneById($id);

//        Récupération des informations sur le projet
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

//            Récupération des images du projet, on met à null si il n'y a pas d'image
//            afin d'éviter les erreurs
        $images = $projet->getImages();
        $logo = null;
        $image1 = null;
        $image2 = null;
        if ( $images->count() == 3 ) //TODO cleaner
        {
            if ($images{0}->getIsLogo()){
                $logo = $images{0};
            } else {
                $image1 = $images{0};
            }
            if ($images{1}->getIsLogo()){
                $logo = $images{1};
            } else {
                if ($image1 != null)
                {
                    $image1 = $images{1};
                } else {
                    $image2 = $images{1};
                }
            }
            if ($images{2}->getIsLogo()){
                $logo = $images{2};
            } else {
                $image2 = $images{2};
            }
        }
        elseif ( $images->count() == 2 )
        {
            if ($images{0}->getIsLogo()){
                $logo = $images{0};
            } else {
                $image1 = $images{0};
            }
            if ($images{1}->getIsLogo()){
                $logo = $images{1};
            } else {
                if ($image1 != null)
                {
                    $image1 = $images{1};
                } else {
                    $image2 = $images{1};
                }
            }
        } elseif ( $images->count() == 1){
            if ($images{0}->getIsLogo()){
                $logo = $images{0};
            } else {
                $image1 = $images{0};
            }
        }

//        Création du template du projet avec les informations de celui-ci
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
                'logo' => $logo,
            ]);

//        Récupération de l'application de génération de pdf
        $html2pdf = $this->get('app.html2pdf');
//        Création d'un pdf
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

//        Génération du pdf et téléchargement de celui-ci
        return $html2pdf->downloadPdf($template, "projetPDF");
    }

    /**
     * @param $idLivret
     *
     *  Controlleur gérant la génération du pdf d'un livret et qui l'affiche
     */
    public function generatorManyAction($idLivret)
    {
//        Récupération du repository des livrets
        $repLivret = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Livret');

//        Récupération du livret que l'on veux générer
        $livret = $repLivret->findOneById($idLivret);

//        Récupération de l'application de génération de pdf
        $html2pdf = $this->get('app.html2pdf');
//        Création du pdf au format A4
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

//        Récupération des projets du livret
        $projets = $livret->getProjets();

//        Récupération des informations pour la page de garde
        $title = $livret->getIntituleLivret();
        $departements = array();
        $promotions = array();
        $intitules = array();
        $minYear = 1000000;
        $maxYear = 0;
        $dateCrea = $livret->getDateCreationLivret()->format('d-m-Y');

//        Récupération des données projets par projets
        foreach ( $projets as $curProj)
        {
            $curForm =$curProj->getEtudiants()[0]->getFormations()[0];
            $curDpt = $curForm->getDepartement()->getNomDpt();
            if ( !in_array($curDpt, $departements))
            {
                array_push($departements, $curDpt);
            }
            $curTypeForm = $curForm->getTypeFormation();
            if ( !in_array($curTypeForm, $promotions))
            {
                array_push($promotions, $curTypeForm);
            }
            $curYearStart = $curForm->getDateDebut()->format('Y');
            $curYearEnd = $curForm->getDateFin()->format('Y');
            if ( $maxYear<$curYearEnd )
            {
                $maxYear = $curYearEnd;
            }
            if( $minYear> $curYearStart)
            {
                $minYear = $curYearStart;
            }
            array_push($intitules, $curProj->getIntituleProjet());
        }

        $template = $this->renderView('::couverture.html.twig',
            [
               'intituleLivret' => $title,
                'minYear' => $minYear,
                'maxYear' => $maxYear,
                'departements' => $departements,
                'promotions' => $promotions,
                'dateCrea' => $dateCrea,
                'intitules' => $intitules,
            ]);
        $html2pdf->write($template);

        $edito = $livret->getEditoLivret();
//        Création du template pour la génération de l'édito du livret
//        si le champ n'est pas vide
        if ( $edito != null and $edito != "") // TODO vérification
        {
            $template = $this->renderView('::edito.html.twig',
                ['texte' => $livret->getEditoLivret(),
                ]);
//            Ecriture du template dans le pdf
            $html2pdf->write($template);
        }

//        Parcours des projets afin d'écrire page par page
        foreach ( $projets as $projet)
        {
//            Récupération des informations sur le projet actuel
            $nomP = $projet->getIntituleProjet();
            $descripP = $projet->getDescripProjet();
            $bilanP = $projet->getBilanProjet();
            $clientP = $projet->getClientProjet();
            $descripCli = $projet->getDescriptionClientProjet();
            $etudiants = $projet->getEtudiants();
            $tuteurs = $projet->getTuteurs();
            $formation = $etudiants{0}->getFormations(){0}->getTypeFormation();
            $departement = $etudiants{0}->getFormations(){0}->getDepartement()->getNomDpt();

//            Récupération des images du projet, on met à null si il n'y a pas d'image
//            afin d'éviter les erreurs
            $images = $projet->getImages();
            $logo = null;
            $image1 = null;
            $image2 = null;
            if ( $images->count() == 3 ) //TODO cleaner
            {
                if ($images{0}->getIsLogo()){
                    $logo = $images{0};
                } else {
                    $image1 = $images{0};
                }
                if ($images{1}->getIsLogo()){
                    $logo = $images{1};
                } else {
                    if ($image1 != null)
                    {
                        $image1 = $images{1};
                    } else {
                        $image2 = $images{1};
                    }
                }
                if ($images{2}->getIsLogo()){
                    $logo = $images{2};
                } else {
                    $image2 = $images{2};
                }
            }
            elseif ( $images->count() == 2 )
            {
                if ($images{0}->getIsLogo()){
                    $logo = $images{0};
                } else {
                    $image1 = $images{0};
                }
                if ($images{1}->getIsLogo()){
                    $logo = $images{1};
                } else {
                    if ($image1 != null)
                    {
                        $image1 = $images{1};
                    } else {
                        $image2 = $images{1};
                    }
                }
            } elseif ( $images->count() == 1){
                if ($images{0}->getIsLogo()){
                    $logo = $images{0};
                } else {
                    $image1 = $images{0};
                }
            }


//            Creation du template pour le projet actuel
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
                    'logo' => $logo,
                ]);

//            Ecriture du template dans le pdf
	        $html2pdf->write($template);
        }

//        Affichage du pdf
        $html2pdf->getOutputPdf("livret");
    }

    /**
     * @param $id
     *
     *  Controlleur gérant la génération d'un livret au format pdf et proposant
     *  le téléchargement de ce dernier
     */
    public function downloadLivretAction($id)
    {
//        Récupération du repository des livrets
        $repLivret = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Livret');

//        Récupération du livret que l'on veux générer
        $livret = $repLivret->findOneById($idLivret);

//        Récupération de l'application de génération de pdf
        $html2pdf = $this->get('app.html2pdf');
//        Création du pdf au format A4
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

//        Récupération des projets du livret
        $projets = $livret->getProjets();

//        Récupération des informations pour la page de garde
        $title = $livret->getIntituleLivret();
        $departements = array();
        $promotions = array();
        $intitules = array();
        $minYear = 1000000;
        $maxYear = 0;
        $dateCrea = $livret->getDateCreationLivret()->format('d-m-Y');

//        Récupération des données projets par projets
        foreach ( $projets as $curProj)
        {
            $curForm =$curProj->getEtudiants()[0]->getFormations()[0];
            $curDpt = $curForm->getDepartement()->getNomDpt();
            if ( !in_array($curDpt, $departements))
            {
                array_push($departements, $curDpt);
            }
            $curTypeForm = $curForm->getTypeFormation();
            if ( !in_array($curTypeForm, $promotions))
            {
                array_push($promotions, $curTypeForm);
            }
            $curYearStart = $curForm->getDateDebut()->format('Y');
            $curYearEnd = $curForm->getDateFin()->format('Y');
            if ( $maxYear<$curYearEnd )
            {
                $maxYear = $curYearEnd;
            }
            if( $minYear> $curYearStart)
            {
                $minYear = $curYearStart;
            }
            array_push($intitules, $curProj->getIntituleProjet());
        }

        $template = $this->renderView('::couverture.html.twig',
            [
                'intituleLivret' => $title,
                'minYear' => $minYear,
                'maxYear' => $maxYear,
                'departements' => $departements,
                'promotions' => $promotions,
                'dateCrea' => $dateCrea,
                'intitules' => $intitules,
            ]);
        $html2pdf->write($template);

        $edito = $livret->getEditoLivret();
//        Création du template pour la génération de l'édito du livret
//        si le champ n'est pas vide
        if ( $edito != null and $edito != "") // TODO vérification
        {
            $template = $this->renderView('::edito.html.twig',
                ['texte' => $livret->getEditoLivret(),
                ]);
//            Ecriture du template dans le pdf
            $html2pdf->write($template);
        }

//        Parcours des projets afin d'écrire page par page
        foreach ( $projets as $projet)
        {
//            Récupération des informations sur le projet actuel
            $nomP = $projet->getIntituleProjet();
            $descripP = $projet->getDescripProjet();
            $bilanP = $projet->getBilanProjet();
            $clientP = $projet->getClientProjet();
            $descripCli = $projet->getDescriptionClientProjet();
            $etudiants = $projet->getEtudiants();
            $tuteurs = $projet->getTuteurs();
            $formation = $etudiants{0}->getFormations(){0}->getTypeFormation();
            $departement = $etudiants{0}->getFormations(){0}->getDepartement()->getNomDpt();

//            Récupération des images du projet, on met à null si il n'y a pas d'image
//            afin d'éviter les erreurs
            $images = $projet->getImages();
            $logo = null;
            $image1 = null;
            $image2 = null;
            if ( $images->count() == 3 ) //TODO cleaner
            {
                if ($images{0}->getIsLogo()){
                    $logo = $images{0};
                } else {
                    $image1 = $images{0};
                }
                if ($images{1}->getIsLogo()){
                    $logo = $images{1};
                } else {
                    if ($image1 != null)
                    {
                        $image1 = $images{1};
                    } else {
                        $image2 = $images{1};
                    }
                }
                if ($images{2}->getIsLogo()){
                    $logo = $images{2};
                } else {
                    $image2 = $images{2};
                }
            }
            elseif ( $images->count() == 2 )
            {
                if ($images{0}->getIsLogo()){
                    $logo = $images{0};
                } else {
                    $image1 = $images{0};
                }
                if ($images{1}->getIsLogo()){
                    $logo = $images{1};
                } else {
                    if ($image1 != null)
                    {
                        $image1 = $images{1};
                    } else {
                        $image2 = $images{1};
                    }
                }
            } elseif ( $images->count() == 1){
                if ($images{0}->getIsLogo()){
                    $logo = $images{0};
                } else {
                    $image1 = $images{0};
                }
            }

//            Creation du template pour le projet actuel
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
                    'logo' => $logo,
                ]);

//            Ecriture du template dans le pdf
            $html2pdf->write($template);
        }
//        Envoie de la demande de téléchargement du pdf
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

