<?php

class SectionController extends App_Controller_Base
{
    use App_Trait_MenuService;

    public function indexAction()
    {
        $this->view->section = App_Map_Section::execute(
            $this->getMenuService()->getSection(
                $this->user,
                $this->getParam('id')
            )
        );
    }

    public function listAction()
    {
        $this->view->sections = App_Map_Section::execute(
            $this->getMenuService()->getSectionList(
                $this->user,
                $this->getParam('parentId')
            )
        );
    }

    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Exception('Unsupported method', 500);
        }
        $section = new App_Model_Section();
        if ($this->getParam('id')) {
            $section = $this->getMenuService()->getSection(
                $this->user,
                $this->getParam('id')
            );
        }

        $this->view->section = App_Map_Section::execute(
            $this->getMenuService()->saveSection(
                $this->user,
                $section,
                App_Model_Section::fetchOne(['id' => $this->getParam('parentId')]),
                $this->getParam('title'),
                $this->getParam('image')
            )
        );
    }

    public function deleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Exception('Unsupported method', 500);
        }
        $this->getMenuService()->deleteSection(
            $this->user,
            App_Model_Section::fetchOne([
                'id' => $this->getParam('id', false)
            ])
        );
    }
} 