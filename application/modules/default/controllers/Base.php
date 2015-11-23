<?php

abstract class App_Controller_Base extends Zend_Controller_Action
{
    /**
     * @var App_Model_User
     */
    public $user = null;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        $userService = new App_Service_User();

        $this->user = $userService->identify($this->getRequest()->getHeader('x-auth', false));

        if (!$this->user) {
            throw new Exception(null, 403);
        }
    }
}