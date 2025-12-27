<?php

namespace App\Controller;

use App\DTO\AddToCartDTO;
use App\FormTypes\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{

    #[Route(path: '/form-html', name: 'app_form_html')]
    public function create_html() : Response
    {
        return $this->render('form-html/form.html.twig');
    }


    #[Route(path: '/form-symfony', name: 'product_show')]
    public function create_symfonyshow(Request $request): Response
    {
        $addToCartDTO = new AddToCartDTO();

        $form = $this->createForm(ProductType::class, $addToCartDTO);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($addToCartDTO);
        }

        return $this->render('form-symfony/form.html.twig', [
            'form' => $form,
        ]);
    }

}
