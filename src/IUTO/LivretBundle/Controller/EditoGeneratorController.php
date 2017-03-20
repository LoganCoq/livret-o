<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Service\HTML2PDF;
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
            ->getRepository('IUTOLivretBundle:Edito');


        $texte = $projet->getEdito();



        $template = $this->renderView('::edito.html.twig',
            [
                'texte' => $texteP,
            ]);

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->generatePdf($template, "editoPDF");
    }
}

