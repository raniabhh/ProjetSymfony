<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Form\BookType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/list', name: 'book_list')]
    public function list(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findAll();

        return $this->render('book/list.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/add', name: 'book_add')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $book = new Book();
        $book->setEnabled(true);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);

            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'book_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $book = $em->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'book_delete')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $book = $em->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $author = $book->getAuthor();
        if ($author) {
            $author->setNbBooks(max(0, $author->getNbBooks() - 1));
        }

        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('book_list');
    }

    #[Route('/show/{id}', name: 'book_show')]
    public function show(BookRepository $bookRepo, int $id): Response
    {
        $book = $bookRepo->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book
        ]);
    }

    #[Route('/list/published', name: 'book_list_published')]
    public function listPublished(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findBy(['enabled' => true]);

        return $this->render('book/list_published.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/authors/delete-zero', name: 'authors_delete_zero')]
    public function deleteAuthorsZero(ManagerRegistry $doctrine, AuthorRepository $authorRepo): Response
    {
        $em = $doctrine->getManager();
        $authors = $authorRepo->findBy(['nbBooks' => 0]);

        foreach ($authors as $author) {
            $em->remove($author);
        }

        $em->flush();

        return $this->redirectToRoute('book_list');
    }
}
