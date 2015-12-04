<?php

/**
 * Class IngredientController
 */
class IngredientController extends App_Controller_Base
{
    use App_Trait_MenuService;
    public function indexAction()
    {
        $this->view->ingredients = App_Map_Ingredient::execute(
            $this->getMenuService()->getIngredients(
                $this->user,
                $this->getParam('search', false)
            )
        );
    }

    public function saveAction()
    {
       if (!$this->getRequest()->isPost()) {
           throw new Exception('Unsupported method');
       }
       $this->view->ingredient = App_Map_Ingredient::execute(
           $this->getMenuService()->saveIngredient(
               $this->user,
               App_Model_Ingredient::fetchOne(['id' => $this->getParam('id', false)]),
               $this->getParam('title', false)
       ));
    }
} 