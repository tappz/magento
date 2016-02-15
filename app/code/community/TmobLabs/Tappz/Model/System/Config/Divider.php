<?php

class TmobLabs_Tappz_Model_System_Config_Divider
{
    /**
     * get option array for system config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value' => '.', 'label' => '. (Dot)');
        $options[] = array('value' => ',', 'label' => ', (Comma)');
        return $options;
    }
}
