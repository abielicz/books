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
     * Connect
     *
     * @param Application $app
     *
     * @return mixed
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

        $controller->match('/{id}/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('comment_add');

        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('comment_edit');

        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('comment_delete');

        return $controller;
    }

    /**
     * Index action
     *
     * @param Application $app
     * @param int $page
     *
     * @return mixed
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


    /**
     * Add action.
     *
     * @param $id
     * @param Application $app
     * @param Request     $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction($id, Application $app, Request $request)
    {
        $comment = [];

        $profileRepository = new ProfileRepository($app['db']);

        $user = $profileRepository->showUserData($app);

        $form = $app['form.factory']->createBuilder(
            CommentType::class,
            $comment
        )->add('user_id', HiddenType::class, ['data'=>$user['id']])
            ->add('topics', HiddenType::class, ['data'=>$id])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository = new CommentRepository($app['db']);
            $commentRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('topic_view', array('id'=>$id)), 301);
        }

        return $app['twig']->render(
            'comment/add.html.twig',
            [
                'comment' => $comment,
                'id' => $id,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Delete action
     *
     * @param Application $app
     * @param $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Application $app, $id, Request $request)
    {
        $commentRepository = new CommentRepository($app['db']);
        $comment = $commentRepository->findOneById($id);
        $topic = $commentRepository->findLinkedTopicsIds($id);
        $profileRepository = new ProfileRepository($app['db']);
        $user = $profileRepository->showUserData($app);

        if(!$comment) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('topic_view', ['id'=>$topic[0]]), 301);
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $comment)->add('id', HiddenType::class,['data'=>$id])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('topic_view', ['id'=>$topic[0]]),
                301
            );
        }

        return $app['twig']->render(
            'comment/delete.html.twig',
            [
                'id' => $id,
                'topic' => $topic[0],
                'user' => $user,
                'created_by' => $comment['user_id'],
                'form' => $form->createView(),
            ]
        );
    }
}