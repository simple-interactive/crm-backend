<?php

class App_Map_Section extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'parentId' => 'parentId',
            'title' => 'title',
            'image' => 'image',
            'productsCount' => 'productsCount'
        ];
    }

    /**
     * @param App_Model_Section $section
     *
     * @return int
     */
    public function getProductsCount(App_Model_Section $section)
    {
        return App_Model_Product::getCount([
            'sectionId' => (string) $section->id
        ]);
    }
} 