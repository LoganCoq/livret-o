<?php

namespace IUTO\LivretBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class PresentationGeneratorController extends Controller
{


    public function generatorAction()
    {

        $texte = $this->get('session')->get('edito');

        $template = $this->renderView('::presentation.html.twig',
            [
                'texte' => $texte,
            ]);

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->generatePdf($template, "presentationPDF");
    }
}

