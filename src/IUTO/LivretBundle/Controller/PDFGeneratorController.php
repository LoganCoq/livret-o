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
        $cpt = 0;
        foreach ($images as $curImg)
        {
            if ($curImg->getIsLogo())
            {
                $logo = $curImg;
            } else {
                if ( $cpt == 0)
                {
                    $image1 = $curImg;
                } else {
                    $image2 = $curImg;
                }
                $cpt++;
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
        $cpt = 0;
        foreach ($images as $curImg)
        {
            if ($curImg->getIsLogo())
            {
                $logo = $curImg;
            } else {
                if ( $cpt == 0)
                {
                    $image1 = $curImg;
                } else {
                    $image2 = $curImg;
                }
                $cpt++;
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
        foreach ($projets as $curProj) {
            $curForm = $curProj->getEtudiants()[0]->getFormations()[0];
            $curDpt = $curForm->getDepartement()->getNomDpt();
            if (!in_array($curDpt, $departements)) {
                array_push($departements, $curDpt);
            }
            $curTypeForm = $curForm->getTypeFormation();
            if (!in_array($curTypeForm, $promotions)) {
                array_push($promotions, $curTypeForm);
            }
            $curYearStart = $curForm->getDateDebut()->format('y');
            $curYearEnd = $curForm->getDateFin()->format('y');
            if ($maxYear < $curYearEnd) {
                $maxYear = $curYearEnd;
            }
            if ($minYear > $curYearStart) {
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

        $editos = $livret->getEditos();

        foreach ($editos as $edito) {
            $template = $this->renderView('::edito.html.twig',
                [
                    'titre' => $edito->getTitre(),
                    'texte' => $edito->getContenuEdito(),
                ]);
//            Ecriture du template dans le pdf
            $html2pdf->write($template);
        }

//        Parcours des projets afin d'écrire page par page
        foreach ($projets as $projet) {
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
            $cpt = 0;
            foreach ($images as $curImg)
            {
                if ($curImg->getIsLogo())
                {
                    $logo = $curImg;
                } else {
                    if ( $cpt == 0)
                    {
                        $image1 = $curImg;
                    } else {
                        $image2 = $curImg;
                    }
                    $cpt++;
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
        $livret = $repLivret->findOneById($id);

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
        foreach ($projets as $curProj) {
            $curForm = $curProj->getEtudiants()[0]->getFormations()[0];
            $curDpt = $curForm->getDepartement()->getNomDpt();
            if (!in_array($curDpt, $departements)) {
                array_push($departements, $curDpt);
            }
            $curTypeForm = $curForm->getTypeFormation();
            if (!in_array($curTypeForm, $promotions)) {
                array_push($promotions, $curTypeForm);
            }
            $curYearStart = $curForm->getDateDebut()->format('Y');
            $curYearEnd = $curForm->getDateFin()->format('Y');
            if ($maxYear < $curYearEnd) {
                $maxYear = $curYearEnd;
            }
            if ($minYear > $curYearStart) {
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

        $editos = $livret->getEditos();

        foreach ($editos as $edito) {
            $template = $this->renderView('::edito.html.twig',
                [
                    'titre' => $edito->getTitre(),
                    'texte' => $edito->getContenuEdito(),
                ]);
//            Ecriture du template dans le pdf
            $html2pdf->write($template);
        }
//        Parcours des projets afin d'écrire page par page
        foreach ($projets as $projet) {
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
            $cpt = 0;
            foreach ($images as $curImg)
            {
                if ($curImg->getIsLogo())
                {
                    $logo = $curImg;
                } else {
                    if ( $cpt == 0)
                    {
                        $image1 = $curImg;
                    } else {
                        $image2 = $curImg;
                    }
                    $cpt++;
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


    public function generatorEditoAction($id)
    {
        //        Récupération du repository des projets
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Edito');

//        Récupération du projet que l'on veut générer
        $edito = $repository->findOneById($id);

        $titre = $edito->getTitre();
        $contenu = $edito->getContenuEdito();

        //        Création du template du projet avec les informations de celui-ci
        $template = $this->renderView('::edito.html.twig',
            [
                'titre' => $titre,
                'texte' => $contenu,
            ]);

//        Récupération de l'application de génération de pdf
        $html2pdf = $this->get('app.html2pdf');
//        Création d'un pdf
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

//        Génération du pdf et affichage
        return $html2pdf->generatePdf($template, "editoPDF");
    }

    public function downloadEditoAction($id)
    {
        //        Récupération du repository des projets
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Edito');

//        Récupération du projet que l'on veut générer
        $edito = $repository->findOneById($id);

        $titre = $edito->getTitre();
        $contenu = $edito->getContenuEdito();

        //        Création du template du projet avec les informations de celui-ci
        $template = $this->renderView('::edito.html.twig',
            [
                'titre' => $titre,
                'texte' => $contenu,
            ]);

//        Récupération de l'application de génération de pdf
        $html2pdf = $this->get('app.html2pdf');
//        Création d'un pdf
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

//        Génération du pdf et affichage
        return $html2pdf->downloadPdf($template, "editoPDF");
    }
}

