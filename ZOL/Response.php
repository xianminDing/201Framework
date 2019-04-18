<?php

/*
|---------------------------------------------------------------
| Response for output data
|---------------------------------------------------------------
| @package ZOL
|
*/

class ZOL_Response
{

	/*
	|---------------------------------------------------------------
	| Response data
	|---------------------------------------------------------------
	| @var array
	|
	*/
	protected $_aProps;

	/*
	|---------------------------------------------------------------
	| HTTP status code
	|---------------------------------------------------------------
	| @var integer
	|
	*/
	protected $_code;

	/*
	|---------------------------------------------------------------
	| Stores output string to be returned to user
	|---------------------------------------------------------------
	| @var string
	|
	*/
	protected $_data;

	/*
	|---------------------------------------------------------------
	| List of messages to be returned to user
	|---------------------------------------------------------------
	| @var array
	|
	*/
	protected $_aMessages;
	
	protected $pageCharset = 'GB2312';

	/*
	|---------------------------------------------------------------
	| HTTP headers
	|---------------------------------------------------------------
	| @var array
	|
	*/
	protected $aHeaders = array();

	protected $contentType = 'html';
	
	protected $_template;

	public $_skinPath = '';#方便指定其他app的模板,tools中使用
    
    protected $switchCtrlArr = array(); #设置part开关的标识数组，用在part的服务降级

    public $extractVarSource = array();#跨不同Part使用变量的来源记录，可以记录哪些变量分别在哪些Part在哪个Part注册的
    public $extractVarName   = array();#哪些变量名可以开放出来，不事先声明，是不能开放出来的
    
    
    public function __construct()
	{
		if (defined('SYSTEM_CHARSET'))
		{
			$this->pageCharset = SYSTEM_CHARSET;
		}
	}
	
	public function set($k, $v)
	{
		$this->_aProps[$k] = $v;
	}

	public function add(array $aData)
	{
		foreach ($aData as $k => $v) {
			$this->_aProps[$k] = $v;
		}
	}
	
    //设置开关变量
    public function setSwitchCtrl(array $arr){
		$this->switchCtrlArr = $arr;
	}
    
	public function setTemplate($string, $tmp = 0)
	{
		if($this->_skinPath != ''){
			$this->_template = $this->_skinPath . $string . '.tpl.php';
		}else{
			$this->_template = APP_PATH . '/Skin/' . $string . '.tpl.php';
		}
	}
	
	public function getTemplate()
	{
		return $this->_template;
	}
	
	public function setMessages(array $aMessages)
	{
		$this->_aMessages = $aMessages;
	}

	/*
	|---------------------------------------------------------------
	| If object attribute does not exist, magically set it to data array
	|---------------------------------------------------------------
	| @param unknown_type $k
	| @param unknown_type $v
	|
	*/
	public function __set($k, $v)
	{
		if (!isset($this->$k)) {
			$this->_aProps[$k] = $v;
		}
	}

	public function __get($k)
	{
		if (isset($this->_aProps[$k])) {
			return $this->_aProps[$k];
		}
	}

	public function getHeaders()
	{
		return $this->aHeaders;
	}

	public function getBody()
	{
		return $this->_aProps;
	}
	public function getOutputBody()
	{
		return $this->_data;
	}
	public function setBody($body)
	{
		$this->_data = $body;
	}

	public function addHeader($header)
	{
		if (!in_array($header, $this->aHeaders)) {
			$this->aHeaders[] = $header;
		}
	}

	public function json($data = '', $message = '', $error = false)
	{
		$this->set('error', $error);
		$this->set('data', $data);
		$this->set('message', $message);
		$this->contentType = 'json';
	}
	public function getContentType()
	{
		return $this->contentType;
	}
	public function setCode($code)
	{
		$this->_code = $code;
	}

	public function __toString()
	{
		return $this->fetch();
	}

    public function session($key, $var)
    {
        session_start();
        $_SESSION[$key] = $var;
    }

    public function cookie($name, $value = null, $expire = 0, $path = null, $domain = '.zol.com.cn', $secure = false, $httponly = false)
    {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

	public function buildStaticPage(array $data, $template, $filePath)
	{
		if (empty($data))
		{
			trigger_error('$data dose not empty!');

			return false;
		}
		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		if (empty($filePath))
		{
			trigger_error('$filePath dose not empty!');

			return false;
		}
		$output = new ZOL_Response();
		$output->add($data);
		$output->template = $template;
		$view = new ZOL_View_Simple($output);

		ZOL_File::write($view->render(), $filePath);

		return false;
	}

	public function fetch()
	{
		if ('' == $this->getTemplate())
		{
			//trigger_error('$template dose not empty!');

			return false;
		}
		$view = new ZOL_View_Simple($this);

		return $view->render();
	}
	public function fetchForAjax($template){

		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
		$data = $view->render();
        return mb_convert_encoding($data, 'UTF-8', 'GBK'); //iconv可能遇到不能转码的字符，请教仲老师后改为此方法20131125 孟同学
	}
    /**
     * ajax的jsonp形式
     */
	public function fetchForAjaxCallBack($template,$callbackName){

		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
		$data = $view->render();
		$data = iconv('GBK', 'UTF-8//IGNORE', $data);
        return $callbackName . '("' .addslashes(str_replace(array("\r","\n"),"",$data)) . '");' ;

	}
      /**
     * ajax的jsonp形式 unicode转骂
     */
	public function fetchForAjaxCallBackUncd($template,$callbackName){

		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
		$data = $view->render();
		$data = json_encode(str_replace(array("\r","\n","\t"),"",iconv('GBK', 'UTF-8//IGNORE', $data)));
        return $callbackName . '(' .$data . ');' ;

	}
    
    /**
     * ESI包含页面
     */
    public function esi($paramArr){
        $options = array(
            'uri'         => false,   #模板名称
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$uri)return false;
        
        return "\n<!--  >>> {$uri} >>> -->\n<esi:include src='{$uri}'/>\n<!--  <<<<<< -->\n";
    }
    
    
    /**
     * 获得part的内容
     */
    public function part($paramArr){
        $options = array(
            'template'         => false,   #模板名称
            'isJs'             => false,   #是否作为js的形式输出
            'switchName'       => false,   #降级的开关名称
            'varCheck'         => false,   #变量验证，如果变量不符合
            'extractVars'      => false,   #不同的part，如果可以通用变量，可以在这里注册，注册的形式 'extractVars' => array("noDocId","hasProIds")
            'noTag'            => false,   #不要区块分界线
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$template)return false;
        
        
        //---------------------------------
        // 开关变量的判断，用于服务区块降级
        //---------------------------------
        if($switchName && !empty($this->switchCtrlArr[$switchName]) ){
           return false;
        }
        
        
        //---------------------------------
        // 变量判断，如果变量不符合条件就跳出
        //---------------------------------
        if($varCheck){
            foreach ($varCheck as $var => $con){
                if(is_array($con)){
                    if(!in_array($this->_aProps[$var], $con))return false;
                }else{
                    if($this->_aProps[$var] != $con)return false;
                }
            }
        }
        
        //---------------------------------
        // 贯通Part的变量处理
        //---------------------------------
        if($extractVars && is_array($extractVars)){
            $this->extractVarName = $extractVars; //将导出的变量记录下来
            
            //将变量的来源注册记录起来
            foreach ($extractVars as $nm){
                $this->extractVarSource[$template][] = $nm;
            }
            
        }

		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
        $startCmt = "\n<!--  >>> PART:{$template} >>> -->\n";
        $endCmt   = "\n<!--  <<< PART:{$template} <<< -->\n";
        if($noTag || (defined('IS_PRODUCTION') && IS_PRODUCTION)){
            $startCmt = $endCmt = '';
        }
		if($isJs){#是否输出为js代码
			return $startCmt . '<script>document.write("'.  addslashes(str_replace(array("\r","\n"),"",$view->render())) . '");</script>' . $endCmt;
		}else{
			return $startCmt . $view->render() . $endCmt;
		}
        
    }
    
	public function fetchCol($template,$js=false)
	{
		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
		if($js){#是否输出为js代码
			return '<script>document.write("'.  addslashes(str_replace(array("\r","\n"),"",$view->render())) . '");</script>';
		}else{
			return $view->render();
		}
	}

	public function display()
	{
		if (function_exists('ob_gzhandler') && defined("IS_PRODUCTION") && IS_PRODUCTION === true)#生产环境开启
		{
			ob_start('ob_gzhandler');
		}
		if (!headers_sent())
		{
			//header('Last-Modified: ' . gmdate('D, d M Y H:i:s', SYSTEM_TIME) . ' CST');
			//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			header('Content-Type: text/html; charset=' . $this->pageCharset);
			
			foreach ($this->getHeaders() as $header)
			{
					header($header);
			}       
		}
		$html = $this->fetch();
		echo $html;
        return $html;
	}
}
