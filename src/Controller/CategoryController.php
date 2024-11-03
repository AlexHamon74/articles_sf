<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category_list')]
    public function category_list(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->render('category/category_list.html.twig', [
            'categories' => $categories,
        ]);
    }
}
