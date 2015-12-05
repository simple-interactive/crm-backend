<?php

/**
 * @class App_Map_Product
 */
class App_Map_Product extends Mongostar_Map_Instance
{
    public static function rulesCommon()
    {
        return [
            'id' => 'id',
            'sectionId' => 'section',
            'title' => 'title',
            'description' => 'description',
            'price' => 'price',
            'weight' => 'weight',
            'images' => 'images',
            'ingredients' => 'ingredients',
            'options' => 'options',
            'exists' => 'exists'
        ];
    }

    /**
     * @param App_Model_Product $product
     *
     * @return array
     */
    public function getIngredients(App_Model_Product $product)
    {
        $ingredients = [];
        foreach ($product->ingredients as $item) {
            $ingredients [] = [
                'ingredient' => App_Map_Ingredient::execute(App_Model_Ingredient::fetchOne(['id' => $item['id']])),
                'price' => $item['price'],
                'weight' => $item['weight']
            ];
        }

        return $ingredients;
    }

    public function getSectionId(App_Model_Product $product)
    {
        return App_Model_Section::fetchOne(['id' => $product->sectionId])->title;
    }
} 