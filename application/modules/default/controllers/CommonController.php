<?php

class CommonController extends App_Controller_Base
{
    public function indexAction()
    {
        $this->view->user = App_Map_User::execute(
            $this->user,
            'auth'
        );
    }
}