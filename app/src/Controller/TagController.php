<?php
/**
 * Tag controller.
 */
namespace Controller;

use Repository\TagRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

/**
 * Class TagController.
 *
 * @package Controller
 */
class TagController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('tag_index');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('tag_view');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function indexAction(Application $app)
    {
        $tagRepository = new TagRepository($app['db']);

        return $app['twig']->render(
            'tag/index.html.twig',
            ['tags' => $tagRepository->findAll()]
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
        $tagRepository = new TagRepository($app['db']);

        return $app['twig']->render(
            'tag/view.html.twig',
            ['tag' => $tagRepository->findOneById($id)]
        );
    }
}