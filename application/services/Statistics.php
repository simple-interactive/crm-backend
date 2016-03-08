<?php

/**
 * @class App_Service_Statistics
 */
class App_Service_Statistics
{

    public function putIntoStatistics(App_Model_Product $product)
    {
        if (! App_Model_STProduct::fetchOne(
            [
                'productId' => (string) $product->id,
                'hashForSt' => $product->hashForSt
            ]
        )
        ){
            $data = $product->asArray();
            unset($data['id']);
            $stproduct = new App_Model_STProduct($data);
            $stproduct->productId = (string) $product->id;
            $stproduct->startDate = new \MongoDate();

            $oldProduct = App_Model_STProduct::fetchAll( [
                'productId' => (string) $product->id
            ], [
                '_id' => -1
            ], 1);
            if (count($oldProduct) == 1) {
                $oldProduct[0]->endDate = new \MongoDate();
                $oldProduct[0]->save();
            }
            $stproduct->save();
        }

        if (!App_Model_STSection::fetchOne([
            'sectionId' => $product->sectionId
        ])) {
            $stsection = new App_Model_STSection();
            $section = App_Model_Section::fetchOne([
                'id' => new \MongoId($product->sectionId)
            ]);
            $stsection->sectionId = $product->sectionId;
            $stsection->title = $section->title;
            $stsection->parentId = $section->parentId;
            $stsection->save();
        }

        if (isset($product->ingredients) && count($product->ingredients) > 0)
            foreach ($product->ingredients as $ingredient) {
                $ingredient = App_Model_Ingredient::fetchOne([
                    'id' => new \MongoId($ingredient['id'])
                ]);
                $stIngredient = App_Model_STIngredient::fetchOne([
                    'ingredientId' => (string) $ingredient->id
                ]);
                if (!$stIngredient) {
                    $stIngredient = new App_Model_STIngredient([
                        'ingredientId' => (string) $ingredient->id,
                        'title' => $ingredient->title
                    ]);
                    $stIngredient->save();
                }
            }

    }
}