<?php

class AuthController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $userService = new App_Service_User();

        $user = $userService->auth(
            $this->getParam('email', false),
            $this->getParam('password', false)
        );

        if ($user) {
            $this->view->success = true;
            $this->view->user = App_Map_User::execute($user, 'auth');
        }
        else {
            $this->view->success = false;
            $this->view->error = $userService->getLastError();
        }
    }
}