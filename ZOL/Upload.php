<?php
/**
* 文件上传类
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2010-03-02
* @version v1.0
*/

class ZOL_Upload
{
	/**
	* data 需要传递的额外数据
	* 
	* @var array
	*/
	private $_data = array();
	
	/**
	* 多文件上传
	* 
	* @var boolean
	*/
	private $_isMultiFile = true;
	
	/**
	* 上传目录
	* 
	* @var string
	*/
	private $_uploadDir = '';
	
	/**
	* 允许上传的文件类型
	* 
	* @var array
	*/
	private $_allowFileTypeArr = array('jpg', 'png', 'gif', 'bmp');
	
	/**
	* 禁止上传的文件类型
	* 
	* @var array
	*/
	private $_forbidFileTypeArr = array('exe');
	
	/**
	* 最大上传文件大小
	* 
	* @var integer
	*/
	private $_maxFileSize = 1048576;#1M
	
	/**
	* 最小上传文件大小
	* 
	* @var integer
	*/
	private $_minFileSize = 0;
	
	/**
	* 上传后转换文件的类型，留空说明不转换，保留原格式
	* 
	* @var string 
	*/
	private $_convFileType = '';
	
	/**
	* 水印设置
	* 
	* @var string
	*/
	private $_watermark = array(
		'file'   => '',   #水印文件
		'offset' => 'RB', #LT左上, LC左中, LB左下, RT右上, RC右中, RB右下, C中部
		'alpha'  => 100,  #水印透明度
	);
	
	/**
	* 生成的缩略图尺寸
	* 
	* @var array
	*/
	private $_thumbSizeArr = array();
	
	private $_fileHandle = null;
	
	/**
	* 整理后的文件信息
	* 
	* @var array
	*/
	private $_tidyFiles = array();
	
	/**
	* 文件保存路径
	* 
	* @var array
	*/
	private $_filePathArr = array();
	
	/**
	* 存储文件对象
	* 
	* @var ZOL_Abstract_Upload
	*/
	private $_saveUploadObj;
	
	/**
	* 生成缩略图回调函数
	* 
	* @var callback
	*/
	private $_makeThumbCallback;
	
	/**
	* 错误代码
	* 
	* @var array
	*/
	private $_errorCode = 0;
	
	/**
	* 文件上传错误常量
	*/
	const ERR_FILE_DATA_SICK = 1;#数据不完整
	const ERR_FILE_SIZE_OVER = 2;#文件大小超出范围
	const ERR_FILE_TYPE      = 4;#文件类型不正确
	
	/**
	* 单例
	* 
	* @var ZOL_Upload
	*/
	private static $_instance = null;
	
	/**
	* 初始化
	* 
	* @param array $config
	* <pre>
	* 	$_FILES $config['fileHandle']     文件句柄
	* 	string  $config['uploadDir']      上传目录
	* 	array   $config['allowFileType']  允许上传的文件类型
	* 	array   $config['forbidFileType'] 禁止上传的文件类型
	* </pre>
	* @return ZOL_Upload
	*/
	public function __construct(array $config = null)
	{
		if (is_array($config)) {
			$this->_set($config);
		}
	}
	
	public static function instance(array $config = null)
	{
		if (self::$_instance === null) {
			self::$_instance = new self($config);
		}
		return self::$_instance;
	}
	
	private function _set(array $config = null)
	{
		foreach ($config as $key => $val) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($val);
			}
		}
		return $this;
	}
	
	public function getErrorCode()
	{
		return $this->_errorCode;
	}
	
	/**
	* 设置额外数据 主要用于传递给子处理类
	* 
	* @param array $fileHandle
	* @return ZOL_Upload
	*/
	public function setData(array $data)
	{
		$this->_data = $data;
		return $this;
	}
	
	public function setIsMultiFile($isMultiFile)
	{
		$this->_isMultiFile = (bool)$isMultiFile;
		return $this;
	}
	
	/**
	* 设置上传文件句柄
	* 
	* @param array $fileHandle
	* @return ZOL_Upload
	*/
	public function setFileHandle(array $fileHandle)
	{
		$this->_fileHandle = $fileHandle;
		$this->_sortFiles();
		return $this;
	}
	
	/**
	* 设置上传目录
	* 
	* @param string $dir
	* @return ZOL_Upload
	*/
	public function setUploadDir($dir)
	{
		$this->_uploadDir = $dir;
		return $this;
	}
	
	/**
	* 设置允许上传的文件类型
	* 
	* @param array $typeArr
	* @return ZOL_Upload
	*/
	public function setAllowFileType(array $typeArr) {
		$this->_allowFileTypeArr = $typeArr;
		return $this;
	}
	
	/**
	* 设置禁止上传的文件类型
	* 
	* @param array $typeArr
	* @return ZOL_Upload
	*/
	public function setForbidFileType(array $typeArr) {
		$this->_forbidFileTypeArr = $typeArr;
		return $this;
	}
	
	/**
	* 设置文件上传后的类型
	* 
	* @param string $type
	* @return ZOL_Upload
	*/
	public function setConvFileType($type)
	{
		$this->_convFileType = $type;
		return $this;
	}
	
	/**
	* 设置存储的回调函数
	* 
	* @param callback $callback
	* @return ZOL_Upload
	*/
	public function setSaveUploadObj(ZOL_Interface_Upload $object)
	{
		$this->_saveUploadObj = &$object;
		return $this;
	}
	
	/**
	* 设置水印属性
	* 
	* @param array $watermark 水印配置
	* @return ZOL_Upload
	*/
	public function setWatermark(array $watermark)
	{
		$this->_watermark = $watermark;
		return $this;
	}
	
	/**
	* 设置生成缩略图尺寸
	* 
	* @param array $size
	* @return ZOL_Upload
	*/
	public function setThumbSize(array $size)
	{
		$this->_thumbSizeArr = $size;
		return $this;
	}
	
	/**
	* 保存上传
	* 
	* @param callback $callback
	* @return ZOL_Upload
	*/
	public function save()
	{
		#存库
		$files = $this->_tidyFiles;
		if (empty($files)) {
			return false;
		}
		
		foreach ($files as $file) {
			#验证
			if (!$this->_validate($file)) {
				continue;
			}
			
			
			/**
			* 返回路径
			* 
			* @var mixed
			*/
			$pathInfo = $this->_saveUploadObj->save($file, $this->_data);
			
			if (!$pathInfo) {
				return false;
			}
			
			list($path, $thumbPath) = $pathInfo;
			$thumbPath = $this->_uploadDir . '/' . $thumbPath;
			$path      = $this->_uploadDir . '/' . $path;
			$dir       = dirname($path);
			
			is_dir($dir)|| ZOL_File::mkdir(dirname($path));
			
			#移动文件
			if (!move_uploaded_file($file['tmp_name'], $path)) {
				$this->_saveUploadObj->rm($path);
				continue;
			}
			chmod($path, 0777);
			if ($this->_thumbSizeArr) {
				foreach ($this->_thumbSizeArr as $size) {
					$_thumbPath = str_replace('{SIZE}', $size, $thumbPath);
					$this->makeThumb($path, $_thumbPath, $size);
				}
			}
			$this->_filePathArr[] = $path;
		}
		return $this;
	}
	
	/**
	* 创建缩略图
	* 
	* @param string $path 原图
	* @param string $toPath 生成后的图
	* @param string $size 图片尺寸
	*/
	public function makeThumb($path, $toPath, $size)
	{
		$size = strtolower($size);
		
		$toDir = dirname($toPath);
		is_dir($toDir) || ZOL_File::mkdir($toDir);
		system("convert -geometry {$size} {$path} {$toPath} ");
		return $this;
	}
	
	public function getFilePathArr()
	{
		return $this->_filePathArr;
	}
	
	/**
	* 整理文件
	* 
	*/
	private function _sortFiles()
	{
		$files = $this->_fileHandle;
		if (empty($files)) {
			return false;
		}
		
		if (!$this->_isMultiFile) {
			$this->_tidyFiles = array($files);
			return $this;
		}
		$tidyFiles = array();
		foreach ($files as $attr => $group) {
			foreach ($group as $key => $one) {
				if (!$one) {
					continue;
				}
				$tidyFiles[$key][$attr] = $one;
			}
		}
		$this->_tidyFiles = $tidyFiles;
		return $this;
	}
	
	/**
	* 验证文件
	* 
	* @param array $file 单个文件信息
	* @return boolean
	*/
	private function _validate(array $file)
	{
		if (empty($file['name']) || empty($file['tmp_name']) || !empty($file['error'])) {
			$this->_errorCode = self::ERR_FILE_DATA_SICK;#数据不完整
			return false;
		}
		
		#文件大小验证
		if (empty($file['size']) || $file['size'] > $this->_maxFileSize) {
			$this->_errorCode = self::ERR_FILE_SIZE_OVER;#文件超过限制
			return false;
		}
		
		
		#文件类型验证
		$extName = self::_getExtName($file['name']);
		if (!in_array($extName, $this->_allowFileTypeArr) || in_array($extName, $this->_forbidFileTypeArr)) {
			$this->_errorCode = self::ERR_FILE_TYPE;#文件类型不正确
			return false;
		}
		return true;
	}
	
	/**
	* 获取文件扩展名
	* 
	* @param string $file
	* @return string
	*/
	private static function _getExtName($file)
	{
		return strtolower(substr(strrchr($file, '.'), 1));
	}
	
	/**
	 * 不通过扩展，通过curl形式上传到fastdfs
	 */
	public static function saveFileToFastDFSByCurl($paramArr) {
	    $options = array(
	        'moduleName'     => 'common', #模块名
	        'filePath'       => '', #本地文件路径
	        'cpng'           => 1,#转换png格式为jpg，默认强制转换
	    );
	    if (is_array($paramArr))$options = array_merge($options, $paramArr);
	    extract($options);
	
	    $ch = curl_init();
	    if (defined("CURLOPT_IPRESOLVE") && defined("CURL_IPRESOLVE_V4")) {
	        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	    }
	    //php5.6及以上版本必须使用CURLFile类进行curl方式的上传
	    $fileHandle = new CURLFile(realpath($filePath));
	    $data = array("uploadModuleName" => $moduleName, "cpng"=>$cpng, "file" => $fileHandle); //文件路径
	    curl_setopt($ch, CURLOPT_URL, "http://upload.fd.zol.com.cn/upload.php");
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $out = curl_exec($ch);
	    if($out){
	        $out = json_decode($out, true);
	        unset($out["fileName"]);
	    }
	    curl_close($ch);
        //删除临时文件
        if($filePath && is_file($filePath)){
            ZOL_File::rm($filePath);
        }
	    return $out;
	}
}
