<?php

class SectionController extends App_Controller_Base
{
    /**
     * @var App_Service_Section;
     */
    public $menuService;

    public function init()
    {
        parent::init();

        $this->menuService = new App_Service_Section();
    }

    public function indexAction()
    {
        $this->view->section = App_Map_Section::execute(
            $this->menuService->getSection(
                $this->user,
                $this->getParam('id')
            )
        );
    }

    public function listAction()
    {
        $this->view->sections = App_Map_Section::execute(
            $this->menuService->getSectionList(
                $this->user,
                $this->getParam('parentId')
            )
        );
    }

    public function saveAction()
    {
        $section = new App_Model_Section();

        if ($this->getParam('id')) {
            $section = $this->menuService->getSection(
                $this->user,
                $this->getParam('id')
            );
        }

        $this->view->section = App_Map_Section::execute(
            $this->menuService->saveSection(
                $this->user,
                $section,
                App_Model_Section::fetchOne(['id' => $this->getParam('parentId')]),
                $this->getParam('title'),
                $this->getParam('image')
            )
        );
    }
} 