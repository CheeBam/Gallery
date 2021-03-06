<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 02.01.16
 * Time: 20:28
 */

namespace Core;

use Core\Controller\AuthController;
use Core\Controller\NotFoundController;

use Lebran\Container\InjectableTrait;
use Lebran\Mvc\View;

class Application
{
    use InjectableTrait;

    public function __construct($di)
    {
        $this->di = $di;
    }

    public function handle()
    {
        $response = $this->di->get('response');

        $components = $this->di->get('router')->execute();
        $components[0] = ($components[0] === '')?'Profile':$components[0];
        $controller = 'Core\\Controller\\' . ucfirst(strtolower($components[0])) . 'Controller';
        if (class_exists($controller)) {
            $controller = new $controller($this->di);
            unset($components[0]);
            if (empty($components[1])){
                $action = 'defaultAction';
                $components = !empty($components[1]) ? $components[1] : '';
            }elseif(preg_match("/\\d+/", $components[1])) {                   //Пушо пхп ругается, на $components[1];
                $action = 'defaultAction';
                $components = !empty($components[1]) ? $components[1] : '';
            }elseif(preg_match("/[a-z,A-z]+/", $components[1])) {
                $action = (strtolower($components[1])) . 'Action';
                unset($components[1]);
                $components = !empty($components[2]) ? $components[2] : '';
            }else {
                $this->notFound('Action');
            }
            if (method_exists($controller, $action)) {
                if ($this->di->get('auth')->isAuthenticated()) {
                    $content = $controller->{$action}($components);
//                    $content = $this->di->call(array($controller, $action), (array)$components );
                    return $response->setContent($content)->send();
                } else {
                    $auth = new AuthController($this->di);
                    return $response->setContent($auth->authAction())->send();
                }
            } else {
                $this->notFound('Action');
            }
        } else {
            $this->notFound('Controller');
        }
    }

    private function notFound($subject){
        $controller = new NotFoundController($this->di);
        $action = 'defaultAction';
        $response = $controller->{$action}($subject);
        return $this->di->get('response')->setStatus(404)->setContent($response)->send();
    }


}