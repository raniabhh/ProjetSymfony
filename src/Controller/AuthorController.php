<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;

use App\Form\AuthorType;

final class AuthorController extends AbstractController
{
  #[Route ('/author/{name}', name:'nom_auteur')]
    public function showAuthor(string $name): Response

  {
    {
        return $this->render('author/show.html.twig', [
            'name' => $name
        ]);
    }
  }
  #[Route ('/authors/list', name:'liste_auteur')]
  public function listAuthors(): Response
  {
    $authors = array(
array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
);

        return $this->render('author/list.html.twig', [
            'authors' => $authors
        ]);

  }
  #[Route ('/authors/details/{id}',name:'id-auteur')]
  public function authorDetails(int $id):Response
  {
     $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' =>
            'victor.hugo@gmail.com', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' =>
            'william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' =>
            'taha.hussein@gmail.com', 'nb_books' => 300),
        );
         $author = null;
        foreach ($authors as $a) {
            if ($a['id'] === $id) {
                $author = $a;
                break;
        }
    }
         if (!$author) {
             throw $this->createNotFoundException('Auteur non trouvé');
    }
    return $this->render('author/showAuthor.html.twig',[
        'author'=>$author
    ]);
  }
  #[Route('/get', name:'get_author')]
  public function getAll(AuthorRepository $authorRepo):Response 
  {
    $authors=$authorRepo->findAll();

    return $this->render('author/index.html.twig', [
      'Controller_name'=>$AuthorController

    ]);
  }
#[Route('/add', name:'add_author')]
public function addAuth(ManagerRegistry $em): Response
{
    $auth1 = new Author();
    $auth1->setUsername('author1');
    $auth1->setEmail('author1@esprit.tn');

    $auth2 = new Author();
    $auth2->setUsername('author2');
    $auth2->setEmail('author2@esprit.tn');

    $em->getManager()->persist($auth1);
    $em->getManager()->persist($auth2);
    $em->getManager()->flush();

    return new Response('add author');
}


 #[Route('/delete/{id}', name: 'app_delete')]
public function deleteAuthor(ManagerRegistry $em, AuthorRepository $authorRepo, int $id): Response
{
    $auth = $authorRepo->find($id);

    if (!$auth) {
        throw $this->createNotFoundException('Auteur non trouvé');
    }

    $em->getManager()->remove($auth);
    $em->getManager()->flush();

    return $this->redirectToRoute('liste_auteur_db');
}

  #[Route('/add/form', name: 'add_author_form')]
public function addAuthor(Request $request, ManagerRegistry $doctrine): Response
{
    $author = new Author();
    $form = $this->createForm(AuthorType::class, $author);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute('liste_auteur_db');
    }

    return $this->render('author/add.html.twig', [
        'form' => $form->createView()
    ]);
}
  #[Route('/edit/{id}', name: 'edit_author')]
public function editAuthor(Request $request, ManagerRegistry $doctrine, int $id): Response
{
    $author = $doctrine->getRepository(Author::class)->find($id);

    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé');
    }

    $form = $this->createForm(AuthorType::class, $author);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $doctrine->getManager()->flush();
        return $this->redirectToRoute('liste_auteur_db');
    }

    return $this->render('author/edit.html.twig', [
        'form' => $form->createView()
    ]);
}
#[Route('/authors/list-db', name:'liste_auteur_db')]
public function listAuthorsFromDB(AuthorRepository $authorRepo): Response
{
   
    $authors = $authorRepo->findAll();

    return $this->render('author/list_db.html.twig', [
        'authors' => $authors
    ]);
}



}
