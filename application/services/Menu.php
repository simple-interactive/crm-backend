<?php

class App_Service_Menu
{
    /**
     * @var string
     */
    private $_lastError;
    /**
     * @var App_Service_Image
     */
    private $imageService;

    public function __construct()
    {
        $this->imageService = new App_Service_Image();
    }


    /**
     * @param App_Model_User $user
     * @param string $title
     * @param string $imageBlob
     *
     * @return App_Model_Menu|bool
     */
    public function create(App_Model_User $user, $title, $imageBlob)
    {
        $this->setLastError(null);
        if (empty($title)){
            $this->setLastError('Title is empty');
            return false;
        }
        $image = $this->imageService->loadImage($imageBlob);
        if(!$image){
            $this->setLastError($this->imageService->getLastError());
            return false;
        }
        $menu = new App_Model_Menu([
            'userId' => (string)$user->id,
            'image' => $image,
            'title' => $title
        ]);
        $menu->save();
        return $menu;
    }

    /**
     * @param App_Model_User $user
     * @return App_Model_Menu[]
     */
    public function getAll(App_Model_User $user)
    {
        return App_Model_Menu::fetchAll([
            'userId' => (string) $user->id
        ]);
    }

    /**
     * @param App_Model_User $user
     * @param string $menuId
     * @param string $title
     * @param string $imageBlob
     *
     * @return App_Model_User|bool|null
     */
    public function edit(App_Model_User $user, $menuId, $title, $imageBlob)
    {
        $menu = App_Model_Menu::fetchOne([
            'id' => $menuId
        ]);
        if ( $menu->userId != (string) $user->id){
            $this->setLastError('Permission denied.');
            return false;
        }
        if (empty($title)){
            $this->setLastError('Title is empty');
            return false;
        }
        $menu->title = $title;
        $image = $this->imageService->loadImage($imageBlob);
        if($image){
            $this->imageService->deleteImageFromStorage($menu->image['identity']);
            $menu->image = $image;
        }
        $menu->save();
        return $menu;
    }

    /**
     * @param string $lastError
     */
    public function setLastError($lastError)
    {
        $this->_lastError = $lastError;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->_lastError;
    }

}