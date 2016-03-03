<?php

class TmobLabs_Tappz_Block_Info extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $content = '
        <div class="tappz">
            <div class="info">
                <p>
                    Please read the installation guide for Magento before starting. &nbsp;
                </p>
                <p>
                <!--
                    <button onclick=\"location.href=\'http://t-appz.com/magento-support-page/\';\">t-appz Installation Guide</button>
                    -->
                    <a href="http://t-appz.com/magento-support-page/" target="_blank">t-appz Installation Guide</a>
                </p>
                <br />
            </div>
        </div>';

        return $content;
    }
}
