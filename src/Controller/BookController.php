<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function createBook(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->isMethod('POST')) {
            $data = $this->extractBookData($request);

            $errors = $this->validateBookData($data);
            if (!empty($errors)) {
                return $this->render('book/create.html.twig', [
                    'errors' => $errors,
                    'book' => $data,
                ]);
            }

            $entityManager = $doctrine->getManager();
            $book = $this->createBookEntity($data);

            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_show_all');
        }

        return $this->render('book/create.html.twig');
    }

    #[Route('/book/show', name: 'book_show_all')]
    public function showAllBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/view.html.twig', [
            'books' => $books,
            'message' => empty($books) ? 'Inga böcker hittades.' : null,
        ]);
    }

    #[Route('/book/show/{id}', name: 'book_show_by_id')]
    public function showBookById(BookRepository $bookRepository, int $id): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Ingen bok hittades för id ' . $id);
        }

        return $this->render('book/show.html.twig', ['book' => $book]);
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
    public function updateBook(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Ingen bok hittades för id ' . $id);
        }

        if ($request->isMethod('POST')) {
            $data = $this->extractBookData($request);

            $errors = $this->validateBookData($data);
            if (!empty($errors)) {
                return $this->render('book/edit.html.twig', [
                    'book' => $data,
                    'errors' => $errors,
                ]);
            }

            $book->setTitle($data['title']);
            $book->setIsbn($data['isbn']);
            $book->setAuthor($data['author']);
            $book->setImage($data['image'] ?? null);

            $entityManager->flush();

            return $this->redirectToRoute('book_show_all');
        }

        return $this->render('book/edit.html.twig', ['book' => $book]);
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

        $this->createExampleBooks($entityManager);

        return $this->redirectToRoute('book_home');
    }

    private function createExampleBooks($entityManager): void
    {
        $booksData = [
            [
                'title' => 'Moby Dick',
                'isbn' => '978-3-16-148410-0',
                'author' => 'Herman Melville',
                'image' => 'mobydick.jpg',
            ],
            [
                'title' => 'Pride and Prejudice',
                'isbn' => '978-1-56619-909-4',
                'author' => 'Jane Austen',
                'image' => 'prideandprjudice.jpg',
            ],
            [
                'title' => '1984',
                'isbn' => '978-0-452-28423-4',
                'author' => 'George Orwell',
                'image' => '1984.jpg',
            ],
        ];

        foreach ($booksData as $data) {
            $book = $this->createBookEntity($data);
            $entityManager->persist($book);
        }

        $entityManager->flush();
    }

    private function createBookEntity(array $data): Book
    {
        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setAuthor($data['author']);
        $book->setImage($data['image'] ?? null);

        return $book;
    }

    private function extractBookData(Request $request): array
    {
        return [
            'title' => trim($request->request->get('title', '')),
            'isbn' => trim($request->request->get('isbn', '')),
            'author' => trim($request->request->get('author', '')),
            'image' => trim($request->request->get('image', '')),
        ];
    }

    private function validateBookData(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Titel måste anges.';
        }
        if (empty($data['isbn'])) {
            $errors[] = 'ISBN måste anges.';
        }
        if (empty($data['author'])) {
            $errors[] = 'Författare måste anges.';
        }

        return $errors;
    }
}
