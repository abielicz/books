<?php
/**
 * Created by PhpStorm.
 * User: Ada
 * Date: 27.06.2017
 * Time: 20:44
 */

namespace Controller;

//use Model\Books\Arr\Books as Books;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\BookRepository;

class BookController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Silex\ControllerCollection Result
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('book_index');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('book_index_paginated');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('book_view');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app, $page = 1)
    {
        $bookRepository = new BookRepository($app['db']);

        return $app['twig']->render(
            'book/index.html.twig',
            ['paginator' => $bookRepository->findAllPaginated($page)]
        );
    }

    public function viewAction(Application $app, $id)
    {
        $bookRepository = new BookRepository($app['db']);

        return $app['twig']->render(
            'book/view.html.twig',
            ['book' => $bookRepository->findOneById($id)]
        );
    }
}
