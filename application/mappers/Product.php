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
            'sectionIdWithTitle' => 'section',
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

    public static function rulesMD()
    {
        return [
            'id' => 'id',
            'sectionId' => 'sectionId',
            'title' => 'title',
            'description' => 'description',
            'price' => 'price',
            'weight' => 'weight',
            'images' => 'images',
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

    /**
     * @param App_Model_Product $product
     * s
     * @return array
     */
    public function getSectionIdWithTitle(App_Model_Product $product)
    {
        $section = App_Model_Section::fetchOne(['id' => $product->sectionId]);
        return [
            'title' => $section->title,
            'id' => (string)$section->id
        ];
    }
} 