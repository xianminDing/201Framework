<?php

/**
 * CRM基类
 * 这里可以放一些验证的东西
 * chenjt <chen.jingtao@zol.com.cn> 
 * 2019年04月16日
 */

class Crm_Page_Abstract extends ZOL_Abstract_Page
{
    /**
	* @var ZOL_DAL_RefreshCacheLoader
	*/
	protected static $_cache;
	
	protected function _loadDb()
	{
	}
    
	public function __construct(ZOL_Request $input, ZOL_Response $output)
	{
		$this->_loadDb();
		$output->execName =
		$input->execName  = $input->getExecName();
		
		$output->actName  =
		$input->actName   = $input->getActionName();
		
		$output->ctlName  =
		$input->ctlName   = $input->getControllerName();
	}
	
    
    /*
     * 公共验证
     */
    public function baseValidate(ZOL_Request $input, ZOL_Response $output) {
        
        //后台没必要设置ts
        Libs_Global_PageHtml::setExpires(0);

        $loginFlag = ZOL_Api::run("Security.Auth.adminIsLogin", array('remoteCheck' => 0, 'recAdminLog' => 0));
        if (!$loginFlag) {
            $this->showHtml("呃……你是不是忘了在admin后台登录? 点↓↓↓↓<br /><a target=\"_blank\" href=\"http://admin.zol.com.cn/login.php\"><img src='http://admin.apicloud.zol.com.cn/img/login1.jpg'  style='border:1px solid #ccc;padding:3px;'></a>");
        }
       
        $output->admin = $input->cookie("S_uid");
        
        #头尾html
        $output->head = $output->fetchCol("Part/Head");
        $output->left = $output->fetchCol("Part/Left");
        $output->foot = $output->fetchCol("Part/Foot");
        return true;
    }

    protected function showHtml($msg) {
        echo '<!DOCTYPE html><html lang="en"><head><title>我了个去...</title></head>';
        echo "<div style='margin:0 auto;text-align:center;padding-top:30px;line-height:40px'>{$msg}</div>";
        echo '</body></html>';
        exit;
    }
    
}