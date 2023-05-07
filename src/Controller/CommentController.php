<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [

        ]);
    }

    #[Route('/create/{id}', name: 'create_comment')]
    public function create(Request $request, EntityManagerInterface $manager, Post $post) :Response
    {
        if (!$post) {
            return $this->redirectToRoute('app_post');
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTime());

            $manager->persist($comment);
            $manager->flush();
        }
        return $this->redirectToRoute('show_post', ['id'=>$comment->getPost()->getId()]);
    }

    #[Route('/edit/{id}', name: 'edit_comment')]
    public function edit(Request $request, EntityManagerInterface $manager, Comment $comment) :Response
    {
        if (!$comment) {
            return $this->redirectToRoute('app_post');
        }

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTime());

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('show_post', ['id'=>$comment->getPost()->getId()]);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'commentForm'=>$commentForm
        ]);
    }

    #[Route('/delete/{id}', name:'delete_comment')]
    public function delete(Comment $comment, EntityManagerInterface $manager) :Response
    {
        if (!$comment) {
            $this->redirectToRoute('show_post');
        }

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('show_post', ['id'=>$comment->getPost()->getId()]);
    }
}
