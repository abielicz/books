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
            'title' => 'PHP manual',
            'url'   => 'http://php.net',
            'tags'  => [
                'PHP',
                'manual',
            ],
        ],
        [
            'title' => 'Silex',
            'url'   => 'http://silex.sensiolabs.org',
            'tags'  => [
                'PHP',
                'framework',
                'Silex',
            ],
        ],
        [
            'title' => 'Learn Git Branching',
            'url'   => 'http://learngitbranching.js.org',
            'tags'  => [
                'tools',
                'Git',
                'VCS',
                'tutorials',
            ],
        ],
        [
            'title' => 'PhpStorm',
            'url'  => 'https://www.jetbrains.com/phpstorm',
            'tags' => [
                'tools',
                'IDE',
                'PHP',
            ],
        ],
        [
            'title' => 'Twig',
            'url'  => 'http://twig.sensiolabs.org',
            'tags' => [
                'tools',
                'templates',
                'Twig',
                'Silex',
                'PHP',
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