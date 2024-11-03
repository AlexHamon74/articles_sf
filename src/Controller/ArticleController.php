<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article_list')]
    public function article_list(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();

        return $this->render('article/article_list.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/create', name: 'app_article_create')]
    public function article_create(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class ,$article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Votre article à bien été crée');
            return $this->redirectToRoute('app_article_list');
        }
        return $this->render('article/article_create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'app_article_edit')]
    public function article_edit(Article $article, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new DateTimeImmutable());
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Votre article à bien été édité');
            return $this->redirectToRoute('app_article_list');
        }
        
        return $this->render('article/article_edit.html.twig', [
            'article' => $article,
            'form' => $form
        ]);
    }
    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function article_delete(Article $article, EntityManagerInterface $em)
    {
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'La recette à bien été supprimée');
        return $this->redirectToRoute('app_article_list');
    }

    #[Route('/article/{id}', name: 'app_article_details')]
    public function article_details(Article $article): Response
    {
        return $this->render('article/article_details.html.twig', [
            'article' => $article,
        ]);
    }
}
