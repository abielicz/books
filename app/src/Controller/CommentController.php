<?php
/**
 * Comment controller.
 */
namespace Controller;

use Repository\CommentRepository;
use Repository\TagRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Form\CommentType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController.
 *
 * @package Controller
 */
class CommentController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('comment_index');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('comment_index_paginated');
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('comment_view');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('comment_add');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function indexAction(Application $app, $page = 1)
    {
        $commentRepository = new CommentRepository($app['db']);

        return $app['twig']->render(
            'comment/index.html.twig',
            ['paginator' => $commentRepository->findAllPaginated($page)]
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
    public function viewAction(Application $app, $id)
    {
        $commentRepository = new CommentRepository($app['db']);

        return $app['twig']->render(
            'comment/view.html.twig',
            ['comment' => $commentRepository->findOneById($id)]
        );
    }

    public function addAction(Application $app, Request $request)
    {
        $comment = [];
        $form = $app['form.factory']->createBuilder(
            CommentType::class,
            $comment,
            ['comment_repository' => new CommentRepository($app['db'])]
        )->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository = new CommentRepository($app['db']);
            $commentRepository->save($form->getData());

            return $app->redirect($app['url_generator']->generate('comment_index'), 301);
        }
        return $app['twig']->render(
            'comment/add.html.twig',
            [
                'comment' => $comment,
                'form' => $form->createView(),
            ]
        );
    }

    public function editAction(Application $app, $id, Request $request)
    {
        $commentRepository = new CommentRepository($app['db']);
        $comment = $commentRepository->findOneById($id);

        if (!$comment) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('comment_index'));
        }

        $form = $app['form.factory']->createBuilder(
            CommentType::class,
            $comment,
            ['comment_repository' => new CommentRepository($app['db'])]
        )->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('comment_index'), 301);
        }

        return $app['twig']->render(
            'comment/edit.html.twig',
            [
                'comment' => $comment,
                'form' => $form->createView(),
            ]
        );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'comment-default',
                'comment_repository' => null,
            ]
        );
    }
}