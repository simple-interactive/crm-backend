<?php

/**
 * Class STSection
 */
class App_Map_STSection extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'title' => 'title'
        ];
    }

    public function getTitle(App_Model_STSection $section)
    {
        $result = $section->title;
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