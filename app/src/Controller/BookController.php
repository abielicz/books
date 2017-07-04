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
use Repository\TagRepository;
use Form\BookType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

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
        $controller->get('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('book_add');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('book_view');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('book_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('book_delete');
  //      $controller->get('book/delete/{id}', [$this, 'viewActionC'])->bind('book_view');



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
            ['book' => $bookRepository->findOneById($id),
            'comment' => $bookRepository->findAllWhere($id)]
        );
    }

    /**
     * View action.
     *
     * @param \Silex\Application $app Silex application
     * @param string             $id  Element Id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
 /*   public function viewActionC(Application $app, $id)
    {
        $bookRepository =new BookRepository($app['db']);

       // dump($bookRepository->findAllWhere($id));

        return $app['twig']->render(
            'book/view.html.twig',
            ['comment' => $bookRepository->findAllWhere($id)]
        );
    }
*/

    public function addAction(Application $app, Request $request)
    {   $book = [];

        $form = $app['form.factory']->createBuilder(
            BookType::class,
            $book,
            ['tag_repository' => new TagRepository($app['db'])]
        )->getForm();
        dump($form);
        dump($book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository = new BookRepository($app['db']);
            $bookRepository->save($form->getData());
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('book_index'), 301);
        }

        return $app['twig']->render(
            'book/add.html.twig',
            [
                'book' => $book,
                'form' => $form->createView(),
            ]
        );
    }

/*
    public function deleteAction(Application $app, $id, Request $request)
    {
        $book = $bookRepository ->findOneById($id);

        if (!$book) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('book_index'));
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $book)->add('id', HiddenType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('category_index'),
                301
            );
        }

        return $app['twig']->render(
            'book/delete.html.twig',
            [
                'book' => $book,
                'form' => $form->createView(),
            ]
        );
    }
*/
}
