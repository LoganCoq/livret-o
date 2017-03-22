<?php

namespace IUTO\LivretBundle\Controller;

use IUTO\LivretBundle\Entity\Livret;
use IUTO\LivretBundle\Service\HTML2PDF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class EditoGeneratorController extends Controller
{

    public function EditoFormAction(Form $form)
    {
        // On crée un objet Advert
        $edito = new Advert();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $edito);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder->add('texte',   TextareaType::class)
            ->add('Visualiser',      SubmitType::class);

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('LivretBundle:EditoGenerator:edito.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function generatorAction()
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IUTOLivretBundle:Livret');


        $texte = "

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vel libero a nunc eleifend porta ut sit amet libero. Donec elementum sapien felis. Nam eget posuere magna, eu fermentum risus. Nullam sit amet eleifend leo, sed pulvinar turpis. Curabitur bibendum egestas dui, quis posuere tellus. Suspendisse potenti. Maecenas egestas maximus orci, sed feugiat quam. Donec sollicitudin est lectus, ac suscipit turpis interdum malesuada. Etiam vel efficitur lorem. In sit amet risus vitae lectus tempus varius. Ut massa nibh, hendrerit id nibh eget, sollicitudin fermentum dolor. Sed dignissim lorem ultrices est volutpat tristique. Vivamus tempor nulla eu turpis convallis, vitae vulputate nibh cursus. Sed malesuada consequat leo, non malesuada orci finibus et. Vestibulum suscipit, turpis in vulputate vestibulum, dolor tortor placerat ante, at scelerisque lorem felis in magna. Ut tellus metus, ultricies quis porttitor at, faucibus eget nulla.

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse ex orci, vehicula quis nulla sed, lobortis feugiat ex. Vivamus sit amet aliquet est, nec fermentum tellus. Quisque gravida ligula vitae sollicitudin aliquet. Phasellus condimentum tempus lobortis. Fusce malesuada magna ut eros pretium cursus. Proin egestas metus eget ante aliquet pellentesque. Sed ornare sed nulla eu bibendum. Maecenas porttitor dui ac dui aliquet, ut pharetra eros imperdiet. Maecenas et lacus nec arcu venenatis maximus aliquam nec mi. Nam posuere dapibus vehicula.

Curabitur quis vestibulum augue, bibendum rhoncus metus. Vestibulum quis velit nulla. Ut quis nulla non ex rhoncus finibus ut sed lorem. Donec convallis, elit nec commodo ullamcorper, sem urna efficitur arcu, at scelerisque felis diam vitae massa. Suspendisse ut vulputate odio. Vestibulum risus arcu, vulputate nec faucibus vel, ultrices vel quam. Maecenas iaculis in quam eu efficitur. Proin in bibendum purus.

Curabitur eu lectus at dui porta sodales. Aliquam porttitor, velit ac ullamcorper suscipit, ligula massa sodales ipsum, vel mollis purus purus id erat. Nullam vitae ultricies metus. Phasellus quam massa, rutrum et fringilla et, consequat id quam. Morbi non ante ut ante dapibus euismod. Quisque enim dolor, iaculis in lobortis vitae, dictum non nibh. Fusce vel turpis laoreet, placerat turpis et, accumsan leo. Morbi faucibus augue egestas, finibus mi mattis, pellentesque elit. Quisque ex augue, laoreet non tortor ut, maximus cursus ex. Pellentesque pellentesque lobortis neque ut volutpat. Etiam ac tellus congue, fringilla velit et, rutrum elit. Nulla justo quam, consectetur et erat et, gravida venenatis justo. Nam auctor erat vel ex varius, id vulputate nulla congue.

Nullam sed elementum leo. Sed faucibus magna felis, eu suscipit augue lacinia id. Nullam pulvinar fringilla tellus, nec convallis magna ultricies ultrices. Nunc non tristique tellus. Vestibulum dignissim hendrerit consequat. Cras malesuada pellentesque lectus sit amet tincidunt. Sed nec ex id turpis congue gravida eget vitae tellus. Etiam ex diam, efficitur a placerat in, tempus ut eros. ";


        $template = $this->renderView('::edito.html.twig',
            [
                'texte' => $texte,
            ]);

        $html2pdf = $this->get('app.html2pdf');
        $html2pdf->create('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));

        return $html2pdf->generatePdf($template, "editoPDF");
    }
}

