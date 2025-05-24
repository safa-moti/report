<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testTitleSetterAndGetter()
    {
        $book = new Book();
        $book->setTitle('1984');

        $this->assertEquals('1984', $book->getTitle());
    }

    public function testIsbnSetterAndGetter()
    {
        $book = new Book();
        $book->setIsbn('978-0451524935');

        $this->assertEquals('978-0451524935', $book->getIsbn());
    }

    public function testAuthorSetterAndGetter()
    {
        $book = new Book();
        $book->setAuthor('George Orwell');

        $this->assertEquals('George Orwell', $book->getAuthor());
    }

    public function testImageSetterAndGetter()
    {
        $book = new Book();
        $book->setImage('1984.jpg');

        $this->assertEquals('1984.jpg', $book->getImage());
    }

    public function testImageSetterAndGetterNull()
    {
        $book = new Book();
        $book->setImage(null);

        $this->assertNull($book->getImage());
    }
}
