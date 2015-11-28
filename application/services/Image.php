<?php

class App_Service_Image
{
    /**
     * @var string
     */
    private $_lastError;

    /**
     * @param string $imageBlob
     *
     * @return array|bool
     */
    public function loadImage($imageBlob)
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
     * @param string $identity
     *
     * @return bool
     */
    public function deleteImageFromStorage($identity)
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