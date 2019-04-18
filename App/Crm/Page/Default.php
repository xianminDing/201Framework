<?php

/*
 * CRM首页控制器
 */

class Crm_Page_Default extends Crm_Page_Abstract
{
    
    public function validate(ZOL_Request $input, ZOL_Response $output) {
        
        $output->cateType = "Index";
        if (!parent::baseValidate($input, $output)) {
            return false;
        }
        return true;
    }
    
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
        
        echo "welcome to ZOL CRM";
        $output->setTemplate("Default");
    }
}