<?php

class MenuController extends App_Controller_Base {

    private $menuService;

    public function init()
    {
        parent::init();
        $this->menuService = new App_Service_Menu;
    }

    public function addAction()
    {
        $menu = $this->menuService->create([
            'userId' => (string) $this->user->id,
            'name' => $this->getParam('name', false)
        ]);

        $this->view->success = true;
        $this->view->menu = App_Map_Menu::execute($menu, 'getOne');
    }

    public function getAction(){
        $menus = $this->menuService->getAll((string)$this->user->id);
        $this->view->success = true;
        $this->view->menus = App_Map_Menu::execute($menus, 'getOne');
    }

    public function deleteAction(){
        $this->view->success = $this->menuService->delete($this->getParam('id', false), (string) $this->user->id);
    }

    public function editAction(){
        try{
            $this->view->success = true;
            $this->view->menu = App_Map_Menu::execute($this->menuService->edit($this->getParam('id', false),
                ['userId' => (string) $this->user->id,
                  'name' => $this->getParam('name', false),
                ]), 'getOne');
        }catch (Exception $e){
            $this->view->success = false;
            throw $e;
        }
    }
} 