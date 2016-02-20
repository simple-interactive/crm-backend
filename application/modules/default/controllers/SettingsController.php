<?php

class SettingsController extends App_Controller_Base
{
    public function indexAction()
    {
        if ($this->_request->isGet()) {
            $setting = App_Model_Settings::fetchOne([
                'userId' => (string) $this->user->id
            ]);

            if ($setting)
                $this->view->settings = $setting->data;
        }
        else {
            $setting = App_Model_Settings::fetchOne([
                'userId' => (string) $this->user->id
            ]);

            if (!$setting) {
                $setting = new App_Model_Settings([
                    'userId' => (string) $this->user->id
                ]);
            }
            $setting->data= $this->getParam('settings', null);
            $setting->save();
        }
    }
}