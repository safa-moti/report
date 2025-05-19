<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BookController extends AbstractController
{

    #[Route('/book', name: 'book_home')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


    #[Route('/book/create', name: 'book_create')]
    public function createBook(ManagerRegistry $doctrine): Response
    {
        if ($_POST) {
            $entityManager = $doctrine->getManager();

            $book = new Book();
            $book->setTitle($_POST['title']);
            $book->setIsbn($_POST['isbn']);
            $book->setAuthor($_POST['author']);
            $book->setImage($_POST['image'] ?? null);

            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_show_all');
        }

        return $this->render('book/create.html.twig');
    }

    // Visa alla böcker
    #[Route('/book/show', name: 'book_show_all')]
    public function showAllBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/view.html.twig', [
            'books' => $books,
            'message' => empty($books) ? 'Inga böcker hittades.' : null
        ]);
    }


    #[Route('/book/show/{id}', name: 'book_show_by_id')]
    public function showBookById(BookRepository $bookRepository, int $id): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Ingen bok hittades för id ' . $id);
        }

        return $this->render('book/show.html.twig', [
            'book' => $book
        ]);
    }


    #[Route('/book/delete/{id}', name: 'book_delete_by_id')]
    public function deleteBookById(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Ingen bok hittades för id ' . $id);
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_show_all');
    }


    #[Route('/book/update/{id}', name: 'book_update')]
    public function updateBook(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Ingen bok hittades för id ' . $id);
        }

        if ($_POST) {
            $book->setTitle($_POST['title']);
            $book->setIsbn($_POST['isbn']);
            $book->setAuthor($_POST['author']);
            $book->setImage($_POST['image'] ?? null);

            $entityManager->flush();

            return $this->redirectToRoute('book_show_all');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book
        ]);
    }


    #[Route('/api/book/books', name: 'api_books')]
    public function apiBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        return $this->json($books, 200, [], ['groups' => 'book:read']);
    }

    #[Route('/api/book/book/{isbn}', name: 'api_book_by_isbn')]
    public function apiBookByIsbn(BookRepository $bookRepository, string $isbn): Response
    {
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);
        if (!$book) {
            throw $this->createNotFoundException('Ingen bok hittades för ISBN ' . $isbn);
        }

        return $this->json($book, 200, [], ['groups' => 'book:read']);
    }


    #[Route('/book/reset', name: 'book_reset')]
    public function resetDatabase(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $books = $entityManager->getRepository(Book::class)->findAll();

        foreach ($books as $book) {
            $entityManager->remove($book);
        }
        $entityManager->flush();

        $this->createExampleBooks($doctrine);

        return $this->redirectToRoute('book_home');
    }


    private function createExampleBooks(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $book1 = new Book();
        $book1->setTitle('Moby Dick');
        $book1->setIsbn('978-3-16-148410-0');
        $book1->setAuthor('Herman Melville');
        $book1->setImage('mobydick.jpg');

        $book2 = new Book();
        $book2->setTitle('Pride and Prejudice');
        $book2->setIsbn('978-1-56619-909-4');
        $book2->setAuthor('Jane Austen');
        $book2->setImage('prideandprjudice.jpg');

        $book3 = new Book();
        $book3->setTitle('1984');
        $book3->setIsbn('978-0-452-28423-4');
        $book3->setAuthor('George Orwell');
        $book3->setImage('1984.jpg');

        $entityManager->persist($book1);
        $entityManager->persist($book2);
        $entityManager->persist($book3);
        $entityManager->flush();
    }
}
