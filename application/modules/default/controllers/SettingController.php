<?php

class SettingController extends App_Controller_Base
{
    public function indexAction()
    {
        if ($this->_request->isGet()) {
            $setting = App_Model_Setting::fetchOne([
                'userId' => (string) $this->user->id
            ]);

            if ($setting)
                $this->view->liqpay = $setting->liqpay;
        }
        else {
            $setting = App_Model_Setting::fetchOne([
                'userId' => $this->user->id
            ]);

            if (!$setting) {
                $setting = new App_Model_Setting([
                    'userId' => (string) $this->user->id
                ]);
            }
            $setting->liqpay = $this->getParam('liqpay', null);
            $setting->save();
        }
    }
}