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
    public function error($error = null, $isParameter = false , $statusCode = 400) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($isParameter) {
            $error = 'param \'' . $error . '\' is missing.';
        }

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
    public function elementNotFound($element,$feminin = false, $statusCode = 404) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($element) {
            $view->setData(array(
                'code' => $statusCode,
                'msg' => $feminin ? $element." non trouvée" : $element. " non trouvé",
            ));
        }

        return $view;
    }

    /**
     * @param string $element
     * @param int $statusCode
     * @return View
     */
    public function warning($element, $statusCode = 500) {
        $view = View::create();
        $view->setStatusCode($statusCode);
        $view->setFormat('json');

        if ($element) {
            $view->setData(array(
                'code' => $statusCode,
                'msg' => $element,
            ));
        }

        return $view;
    }

    /**
     * @param string $element
     * @param int $statusCode
     * @return View
     */
    public function empty() {
        $view = View::create();
        $view->setFormat('json');
        $view->setData(array());

        return $view;
    }
}