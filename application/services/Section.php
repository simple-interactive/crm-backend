<?php

class App_Service_Section
{
    /**
     * @var App_Service_Image
     */
    private $_imageService;

    public function __construct()
    {
        $this->_imageService = new App_Service_Image();
    }

    /**
     * @param App_Model_User $user
     * @param string $sectionId
     *
     * @return App_Model_Section|false
     * @throws Exception
     */
    public function getSection(App_Model_User $user, $sectionId)
    {
        $section = App_Model_Section::fetchOne([
            'userId' => (string)$user->id,
            'id' => $sectionId
        ]);

        if (!$section) {
            throw new Exception('section-not-found', 400);
        }

        return $section;
    }

    /**
     * @param App_Model_User $user
     * @param string $parentId
     *
     * @return App_Model_Section[]
     * @throws Exception
     */
    public function getSectionList(App_Model_User $user, $parentId = null)
    {
        $condition = [
            'userId' => (string) $user->id,
            'parentId' => $parentId
        ];

        if (!empty($parentId)) {
            $parentSection = App_Model_Section::fetchOne([
                'userId' => (string)$user->id,
                'id' => $parentId
            ]);

            if (!$parentSection) {
                throw new Exception('parent-section-not-found', 400);
            }
            $condition['parentId'] = $parentId;
        }

        return App_Model_Section::fetchAll($condition);
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Section $section
     * @param App_Model_Section $parentSection
     * @param string $title
     * @param string $imageBlob
     *
     * @return App_Model_Section
     * @throws Exception
     */
    public function saveSection(
        App_Model_User $user,
        App_Model_Section $section,
        App_Model_Section $parentSection = null,
        $title,
        $imageBlob
    ) {
        // Check if section belongs to user
        if (!empty($section->userId) && $section->userId != (string)$user->id) {
            throw new Exception('user-is-invalid', 400);
        }

        // Check if parent section belongs to user
        if (!empty($parentSection)) {
            if ($parentSection->userId != (string)$user->id) {
                throw new Exception('parent-id-is-invalid');
            }
            $section->parentId = (string)$parentSection->id;
        }

        $section->userId = (string)$user->id;
        $section->title = $title;

        // Checking title validity
        if (mb_strlen($section->title, 'UTF-8') < 3 || mb_strlen($section->title, 'UTF-8') > 30){
            throw new Exception('title-is-invalid', 400);
        }

        // Uploading new image and deleting old image
        if (!empty($imageBlob)) {
            $image = $this->_imageService->loadImage($imageBlob);
            if (!empty($section->image)) {
                $this->_imageService->deleteImageFromStorage($section->image['identity']);
            }
            $section->image = $image;
        }

        // Checking image validity
        if (!$section->image) {
            throw new Exception('image-is-invalid', 400);
        }

        $section->save();
        return $section;
    }
} 