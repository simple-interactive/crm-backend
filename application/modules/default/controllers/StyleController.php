<?php

class StyleController extends App_Controller_Base
{
    use App_Trait_MenuService;

    public function indexAction()
    {

    }

    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Exception('unsupported-method');
        }
        $style = $this->getMenuService()->saveStyle($this->user,
            $this->getParam('colors', false),
            $this->getParam('backgroundImage', false),
            $this->getParam('company', false)
        );
        $this->view->style = App_Map_Style::execute($style);
    }
} 