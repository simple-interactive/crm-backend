<?php

/**
 * @class App_Trait_SearchProduct
 */
trait App_Trait_SearchProduct
{
    /**
     * @param App_Model_Product $product
     *
     * @return App_Model_Search
     */
    public function toSearchModel(App_Model_Product $product)
    {
        $search =  App_Model_Search::fetchOne([
            'productId' => (string) $product->id
        ]);
        $section = App_Model_Section::fetchOne([
            'id' => $product->sectionId
        ]);
        $ingredients =  $product->ingredients;
        $ids = [];
        foreach ($ingredients as $item) {
            $ids [] = $item ['id'];
        }
        $ingredients = App_Model_Ingredient::fetchAll(['id' => ['$in' => $ids]]);
        $ingredientsStr = '';
        foreach ($ingredients as $item) {
            $ingredientsStr .= $item->title . ' ';
        }
        $optionsStr = '';
        foreach ($product->options as $item) {
            $optionsStr .= $item['title']. ' ';
        }
        if (!$search) {
            $search = new App_Model_Search([
                'productId' => (string) $product->id
            ]);
        }
        $data = [
            $product->title,
            $product->description,
            $section->title,
            $ingredientsStr,
            $optionsStr
        ];
        $search->data = implode(' ', $data);
        $search->save();
        return $search;
    }
} 