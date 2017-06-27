<?php
/**
 * Created by PhpStorm.
 * User: Ada
 * Date: 27.06.2017
 * Time: 20:44
 */

namespace Controller;

use Model\Books\Arr\Books;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

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
        $controller->get('/', [$this, 'indexAction']);

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app)
    {
        $bookModel = new Books();

        return $app['twig']->render(
            'book/index.html.twig',
            ['books' => $bookModel->findAll()]
        );
    }

    public function viewAction(Application $app, $id)
    {
        $booksModel = new Books();
        return $app['twig']->render(
            'book/view.html.twig',
            ['book' => $booksModel->findOneById($id)]
        );
    }
}
