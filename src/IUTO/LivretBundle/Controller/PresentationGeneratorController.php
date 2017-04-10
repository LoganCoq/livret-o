<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Livret;
use IUTO\LivretBundle\Service\HTML2PDF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class PresentationGeneratorController extends Controller
{


    public function generatorAction()
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Livret');


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

