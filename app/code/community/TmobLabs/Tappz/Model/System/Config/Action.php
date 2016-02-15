<?php

class TmobLabs_Tappz_Model_System_Config_Action
{
    /**
     * get option array for system config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value' => 'search', 'label' => 'Search a phrase');
        $options[] = array('value' => 'product', 'label' => 'Go to a Product');
        $options[] = array('value' => 'webview', 'label' => 'Open a Url');
        return $options;
    }
}
