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
    public function success($data = null, $statusCode = 200) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($data) {
            $view->setData(array(
                'result' => $data
            ));
        }

        return $view;
    }

    /**
     * @param null $error
     * @param int $statusCode
     * @param array $groups
     * @return View
     */
    public function error($error = null, $statusCode = 400) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($error) {
            $view->setData(array(
                'error' => $error
            ));
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