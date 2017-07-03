<?php
/**
 * Created by PhpStorm.
 * User: Ada
 * Date: 01.07.2017
 * Time: 04:52
 */

namespace Controller;

use Form\PhotoType;
use Repository\PhotoRepository;
use Service\FileUploader;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PhotoController.
 *
 * @package Controller
 */
class PhotoController implements ControllerProviderInterface
{
// ...
    /**
     * Add action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('photo_index');
        $controller->match('/{id}', [$this, 'viewAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('photo_view');
        $controller->get('/tag/{id}', [$this, 'tagAction'])->bind('photo_tag');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('photo_index_paginated');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('photo_edit');
        $controller->match('/{id}/delete', [$this,'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('photo_delete');
        $controller->match('/{id}/comment/delete', [$this,'deleteCommentAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('comment_delete');


        return $controller;
    }
    public function addAction(Application $app, Request $request)
    {
        $photo = [];

        $form = $app['form.factory']->createBuilder(PhotoType::class, $photo)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo  = $form->getData();
            $fileUploader = new FileUploader($app['config.photos_directory']);
            $fileName = $fileUploader->upload($photo['photo']);
            $photo['photo'] = $fileName;
            $photoRepository = new PhotoRepository($app['db']);
            $photoRepository->save($photo);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('photo_index'),
                301
            );
        }

        return $app['twig']->render(
            'photo/add.html.twig',
            [
                'photo'  => $photo,
                'form' => $form->createView(),
            ]
        );
    }
// ...
}