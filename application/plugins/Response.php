<?php

class App_Plugin_Response extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param Zend_Controller_Request_Abstract $request
     * @throws Zend_Controller_Action_Exception
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        Zend_Layout::startMvc();
        Zend_Layout::getMvcInstance()->disableLayout();
        Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->setNeverRender(true);
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        $response->setHeader('content-type', 'application/json', true);
        $response->setBody(
            json_encode(
                 Zend_Layout::getMvcInstance()->getView()->getVars()
            )
        );
    }
}