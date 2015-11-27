<?php

class App_Service_Menu
{
    /**
     * @var string
     */
    private $_lastError;

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
        $image = $this->_loadImage($imageBlob);
        if(!$image){
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
     * @param string $imageBlob
     * @return array|bool
     */
    private function _loadImage($imageBlob)
    {
        $data = explode(',', $imageBlob);
        if (count($data) != 2){
            $this->setLastError('Image invalid');
            return false;
        }
        $image = base64_decode($data[1]);
        $storage = new \Storage\Storage();
        $files =  $storage->upload([
            'content' => $image,
            'name' => md5(microtime()).'.png'
        ], \Storage\Storage::DATA);
        return $files[0]->asArray();
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
        $image = $this->_loadImage($imageBlob);
        if($image){
            $this->_deleteImageFromStorage($menu->image['identity']);
            $menu->image = $image;
        }
        $menu->save();
        return $menu;
    }

    /**
     * @param string $identity
     *
     * @return bool
     */
    private function _deleteImageFromStorage($identity)
    {
        $storage = new \Storage\Storage();
        return $storage->delete($identity);
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