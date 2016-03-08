<?php

/**
 * Class STProduct
 */
class App_Map_STProduct extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'price' => 'price',
        ];
    }

    public function getTitle(App_Model_STProduct $product)
    {
        $section = App_Model_Section::fetchOne([
            'id' => new \MongoId($product->sectionId)
        ]);

        $result = $section->title . '::' . $product->title;
        while (isset($section->parentId)) {
            $section = App_Model_Section::fetchOne([
                'id' => new \MongoId($section->parentId)
            ]);

            if (! $section)
                break;
            $result = $section->title . '::' . $result;
        }
        return $result;
    }
}