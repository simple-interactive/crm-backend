<?php

class ErrorController extends Zend_Controller_Action
{
    /**
     * Initialize error description
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {

             $this->view->code = 500;
             $this->view->message = 'You have reached the error page';

            return;
        }

        switch ($errors->type) {

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';

                break;

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:

                $this->getResponse()->setHttpResponseCode(500);
                if (in_array($errors['exception']->getCode(), [400, 403, 404, 500])) {
                    $this->getResponse()->setHttpResponseCode($errors['exception']->getCode());
                }
                $this->view->message = $errors['exception']->getMessage();

                break;

            default:

                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
        }

        if ($this->getInvokeArg('displayExceptions')) {
            $this->view->exception = $errors['exception']->getTraceAsString();
        }
    }
}