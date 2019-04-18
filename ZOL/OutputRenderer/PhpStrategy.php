<?php

class ZOL_OutputRenderer_PhpStrategy extends ZOL_Abstract_OutputRendererStrategy
{

	public function render(ZOL_Abstract_View $view)
	{
		$php = $this->_initEngine($view->data);
		if (!ZOL_File::exists($php->getTemplate()))
		{
			throw new ZOL_Exception('The template dose not exist or is not readable: ' . $php->getTemplate());
		}
		$variables = $php->getBody();
		if (!empty($variables))
		{
			extract($variables);
		}
		ob_start();
		include $php->getTemplate();
		$content = ob_get_contents();
        $content = preg_replace_callback('#http://([^/]*)\.zol-img\.com\.cn#i','self::picUrlCallback',$content);
        $content = preg_replace_callback('#(<a[^<>]+href\s*=\s*)(["\']*)(http:)([^"\'><]*?\2)#m','self::aUrlCallback',$content);
		ob_end_clean();
        
        /**
         * 模板内变量导出处理
         */
        if(!empty($php->extractVarName) && !empty($extractVars)){
            
            //将要导出的变量，绑定在output上
            foreach($php->extractVarName as $k){
                $php->$k = $extractVars[$k];
            }
            
        }
        //将变量名声明置空，放置每次包含文件都进行变量的处理
        $php->extractVarName = false;
        
		return $content;
	}

	protected function _initEngine(ZOL_Response $response)
	{
		return $response;
	}
    
    protected function picUrlCallback($match){
        $match[1] = preg_replace('#i[\d]+.#','',$match[1]);
        return 'https://'.str_replace('.', '-', $match[1]).'.zol-img.com.cn';
    }
    protected function aUrlCallback($match){
        $m4 = $match[4];
        if (preg_match('#zol-img\.com\.cn|m.zol\.com\.cn|app\.zol\.com\.cn|wap\.zol\.com\.cn#',$m4)){
            return $match[1].$match[2].$match[4];
        }else{
            return $match[0];
        }
    }
}


