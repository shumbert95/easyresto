<?php

namespace AppBundle\Helper;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;

class ApiHelper
{
    /**
     * @param null $data
     * @param int $statusCode
     * @param array $groups
     * @return View
     */
    public function success($data = null, $statusCode = 200, $groups = array()) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($data) {
            $view->setData(array(
                'result' => $data
            ));
        }

        if ($groups) {
            if (!is_array($groups)) {
                $groups = array($groups);
            }
            $context = new Context();
            $context->setGroups($groups);
            $view->setContext($context);
        }

        return $view;
    }

    /**
     * @param null $error
     * @param int $statusCode
     * @param array $groups
     * @return View
     */
    public function error($error = null, $statusCode = 400, $groups = array()) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($error) {
            $view->setData(array(
                'error' => $error
            ));
        }

        if ($groups) {
            if (!is_array($groups)) {
                $groups = array($groups);
            }
            $context = new SerializationContext();
            $context->setGroups($groups);
            $view->setSerializationContext($context);
        }

        return $view;
    }

    public function ok($message = 'OK', $statusCode = 200)
    {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        $view->setData(array(
            'result' => $message
        ));

        return $view;
    }

    /**
     * @param string $element
     * @param int $statusCode
     * @return View
     */
    public function elementNotFound($element, $statusCode = 404) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($element) {
            $view->setData(array(
                'result' => $element . ' not found.'
            ));
        }

        return $view;
    }
}