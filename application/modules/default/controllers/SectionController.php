<?php

class SectionController extends App_Controller_Base
{
    /**
     * @var App_Service_Section;
     */
    private $_sectionService;

    public function init()
    {
        parent::init();
        $this->_sectionService = new App_Service_Section();
    }

    public function saveAction()
    {
        if(!$this->getParam('id', false)){
            $section = $this->_sectionService->add(
                App_Model_Menu::fetchOne(['id' => $this->getParam('menuId', false)]),
                $this->user,
                $this->getParam('title', false),
                $this->getParam('image', false),
                $this->getParam('parentId', false)
            );
        }
        else {
            $section = $this->_sectionService->edit(
                $this->getParam('id', false),
                $this->user,
                $this->getParam('title', false),
                $this->getParam('image', false),
                $this->getParam('parentId', false)
            );
        }
        if ($section){
            $this->view->success = true;
            $this->view->section = App_Map_Section::execute($section);
        }
        else{
            $this->view->success = false;
            $this->view->error = $this->_sectionService->getLastError();
        }
    }

    public function getAction()
    {
        $sections = $this->_sectionService->get(
            $this->user,
            App_Model_Menu::fetchOne(['id' => $this->getParam('menuId', false)])
         );
        $this->view->success = true;
        $this->view->sections = App_Map_Section::execute($sections);
    }

} 