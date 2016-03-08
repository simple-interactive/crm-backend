<?php

/**
 * @class App_Service_Statistics
 */
class App_Service_Statistics
{

    public function putIntoStatistics(App_Model_Product $product, App_Model_Order $order)
    {
        $data = $product->asArray();
        unset($data['id']);
        $stproduct = new App_Model_STProduct($data);
        $stproduct->orderId = (string) $order->id;
        $stproduct->productId = (string) $product->id;
        $stproduct->userId = $order->userId;
        $stproduct->createdAt = new \MongoDate();
        $stproduct->save();

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
            $stsection->userId = $order->userId;
            $stsection->save();
        }

        if (isset($product->ingredients) && count($product->ingredients) > 0)
            foreach ($product->ingredients as $ingredient) {
                $ingredient = App_Model_Ingredient::fetchOne([
                    'id' => new \MongoId($ingredient['id'])
                ]);
                $stIngredient = App_Model_STIngredient::fetchOne([
                    'ingredientId' => (string) $ingredient->id,
                    'userId' => $order->userId
                ]);
                if (!$stIngredient) {
                    $stIngredient = new App_Model_STIngredient([
                        'ingredientId' => (string) $ingredient->id,
                        'title' => $ingredient->title,
                        'userId' => $order->userId
                    ]);
                    $stIngredient->save();
                }
            }

    }
}