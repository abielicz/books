<?php
/**
 * Auth controller.
 *
 */

namespace Controller;

//use Form\LoginType;
//use Form\RegisterType;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\UserRepository;

/**
 * Class AuthController
 *
 * @package Controller
 */
class AuthController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('login', [$this, 'loginAction'])
            ->method('GET|POST')
            ->bind('auth_login');
        $controller->get('logout', [$this, 'logoutAction'])
            ->bind('auth_logout');
        $controller->match('register', [$this, 'registerAction'])
            ->method('GET|POST')
            ->bind('auth_register');

        return $controller;
    }

    /**
     * Login action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function loginAction(Application $app, Request $request)
    {
        $user = ['login' => $app['session']->get('_security.last_username')];
        //Two types, because we have two sites 1.Login 2.Register
        $loginForm = $app['form.factory']->createBuilder(LoginType::class, $user)->getForm();
        $registerForm = $app['form.factory']->createBuilder(RegisterType::class, $user)->getForm();

        return $app['twig']->render(
            'auth/login.html.twig',
            [
                'loginForm' => $loginForm->createView(),
                'registerForm' => $registerForm->createView(),
                'error' => $app['security.last_error']($request),
            ]
        );
    }

    /**
     * Logout action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function logoutAction(Application $app)
    {
        $app['session']->clear();

        return $app['twig']->render('auth/logout.html.twig', []);
    }

    public function registerAction(Application $app, Request $request)
    {
        $user = [];
        $loginForm = $app['form.factory']->createBuilder(LoginType::class, $user)->getForm();
        $registerForm = $app['form.factory']->createBuilder(RegisterType::class, $user,
            ['user_repository' => new UserRepository($app['db'])])->getForm();
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $user  = $registerForm->getData();
            $userRepository = new UserRepository($app['db']);
            $userRepository->add($app, $user);
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.account_successfully_created',
                ]
            );
            return $app->redirect($app['url_generator']->generate('auth_login'), 301);
        }
        return $app['twig']->render(
            'auth/login.html.twig',
            [
                'loginForm' => $loginForm->createView(),
                'registerForm' => $registerForm->createView(),
                'error' => $app['security.last_error']($request),
            ]
        );
    }
}
