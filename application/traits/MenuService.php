<?php

/**
 * @trait App_Trait_MenuService
 */
trait App_Trait_MenuService
{
    /**
     * @var App_Service_Menu
     */
    private $_menuService;

    /**
     * @return App_Service_Menu
     */
    public function getMenuService()
    {
        if ($this->_menuService == null){
            $this->_menuService = new App_Service_Menu();
        }
        return $this->_menuService;
    }

} 