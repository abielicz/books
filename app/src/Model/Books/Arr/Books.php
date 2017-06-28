<?php

/**
 * Created by PhpStorm.
 * User: Ada
 * Date: 27.06.2017
 * Time: 20:50
 */
namespace  Model\Books\Arr;


class Books
{
    protected $books = [
        [
            'title' => 'Nostalgia Anioła',
            'author'=>'Alice Sebold',
            'tags'  => [
                'dramat',
                'psychologiczny',
            ],
        ],
        [
            'title' => 'Córeczka',
            'author' => 'Alice Sebold',
            'tags'  => [
                'dramat',
                'psychologiczny',
                'tragedia',
            ],
        ],
        [
            'title' => 'Baśniarz',
            'author'   => 'Antonia Michaelis',
            'tags'  => [
                'dramat',
                'psychologiczny',
                'kryminał',
            ],
        ],
        [
            'title' => 'Gildia Magów',
            'author'  => 'Trudi Canavan',
            'tags' => [
                'fantastyka',
                'dramat',
            ],
        ],
        [
            'title' => 'Córka kata',
            'author'  => 'Oliwier Potzsch',
            'tags' => [
                'fantastyka',
                'kryminał',
                'psychologiczny',
            ],
        ],
    ];

    /**
     * Find all books.
     *
     * @return array Result
     */
    public function findAll()
    {
        return $this->books;
    }

    /**
     * Find book by its id.
     *
     * @param integer $id Book id
     *
     * @return array Result
     */
    public function findOneById($id)
    {
        $book = [];

        if (isset($this->books[$id]) && count($this->books[$id])) {
            $book = $this->books[$id];
        }

        return $book;
    }

}