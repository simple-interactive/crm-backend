<?php

class MenuController extends App_Controller_Base
{

    /**
     * @var App_Service_Menu
     */
    private $_menuService;

    public function init()
    {
        parent::init();
        $this->_menuService = new App_Service_Menu();
    }

    public function saveAction()
    {
        if (!$this->getParam('id', false)) {
            $menu = $this->_menuService->create(
                $this->user,
                $this->getParam('title', false),
                $this->getParam('image', false)
            );
        }
        else {
            $menu = $this->_menuService->edit(
                $this->user,
                $this->getParam('id', false),
                $this->getParam('title', false),
                $this->getParam('image', false)
            );
        }
        if (!$menu) {
            $this->view->success = false;
            $this->view->error = $this->_menuService->getLastError();
        }
        else {
            $this->view->success = true;
            $this->view->menu = App_Map_Menu::execute($menu);
        }
    }

    public function getAction()
    {
        $menus = $this->_menuService->getAll($this->user);
        $this->view->success = true;
        $this->view->menus = App_Map_Menu::execute($menus);
    }

} 