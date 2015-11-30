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
            $section = $this->_sectionService->create(
                $this->user,
                $this->getParam('title', false),
                $this->getParam('image', false),
                $this->getParam('parentId', null)
            );
        }
        else {
            $section = $this->_sectionService->edit(
                $this->getParam('id', false),
                $this->user,
                $this->getParam('title', false),
                $this->getParam('image', false),
                $this->getParam('parentId', null)
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
            $this->getParam('parentId', null)
        );
        $this->view->success = true;
        $this->view->sections = App_Map_Section::execute($sections);
    }

} 