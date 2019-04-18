<?php

/**
 * Created by PhpStorm.
 * User: zol
 * Date: 2019/2/13
 * Time: 10:05
 */
class Libs_Global_ImageResize
{
	private $localimage;//原图路径
	private $remoteimage;//缩略图保存路径
	private $localinfo;//原图属性
	private $error;

	function resize($localimg, $remoteimg)
	{
		//检测是否支持gd图像处理
		if (!$this->_checkenv()) {
			return false;
		}
		$this->localimage = $localimg;
		$this->remoteimage = $remoteimg;
		$this->localinfo = getimagesize($this->localimage); //获取本地图像的信息
		return $this->_resize($this->localinfo[0], $this->localinfo[1]);
	}

	/**
	 * 检测当前环境是否支持GD
	 */
	private function _checkenv()
	{
		if (!function_exists('gd_info')) {
			$this->error[] = "当前环境不支持GD图像处理，请先安装GD库并开启PHP相关扩展";
			return false;
		}
		return true;
	}

	/**
	 * 生成缩略图主函数
	 * @param int $x 指定的缩略图宽度
	 * @param int $y 指定的缩略图高度
	 * @return boolean
	 */
	private function _resize($x, $y)
	{
		if (!$this->localinfo) {
			$this->error[] = "本地图像文件不存在";
			return false;
		}
		//创建图像句柄
		$im = @$this->_create($this->localinfo[2]);
		if (!$im) {
			$this->error[] = "当前GD库不支持图像类型：{$this->localinfo['mime']}";
			return false;
		}
		$dstsize = $this->_dstsize($x, $y);
		$dstim = @imagecreatetruecolor($dstsize["width"], $dstsize["height"]);
		$whitecolor = @imagecolorallocatealpha($dstim, 0, 0, 0, 127);
		imagefill($dstim, 0, 0, $whitecolor);
		$re = @imagecopyresampled($dstim, $im, 0, 0, 0, 0, $dstsize["width"], $dstsize["height"], $this->localinfo[0], $this->localinfo[1]);
		if (!$re) {
			$this->error[] = "图像重新采样失败";
			return false;
		}
		if (!imagejpeg($dstim, $this->remoteimage)) {
			if (!imagepng($dstim, $this->remoteimage)) {
				if (!imagegif($dstim, $this->remoteimage)) {
					$this->error[] = "保存缩略图到{$this->remoteimage}失败,请检查gd环境是否正常和缩略图文件夹的写入权限。";
					return false;
				}
			}
		}
		$this->error[] = "success";
		return array(
			'width'  => $dstsize["width"],
			'height' => $dstsize["height"]

		);
	}

	/**
	 * 根据本地图片类型，创建图片资源
	 * @param 图像类型代码 $code
	 * @return resource/boolean 成功则返回resourse失败则返回false
	 */
	private function _create($code)
	{
		$src = $this->localimage;
		switch ($code) {
			case 1:
				return imagecreatefromgif($src);
				break;
			case 2:
				return imagecreatefromjpeg($src);
				break;
			case 3:
				return imagecreatefrompng($src);
				break;
			default :
				return false;
				break;
		}
	}

	/**
	 * 按比例计算合适的宽度
	 * @param int $x 指定的缩略图宽度
	 * @param int $y 指定的缩略图高度
	 * @return array 包含调整后的缩略图宽度和高度
	 */
	private function _dstsize($x, $y)
	{
		list($srcwidth, $srcheight) = $this->localinfo;
		if (($srcwidth / $srcheight) < ($x / $y)) {
			$x = floor($y * $srcwidth / $srcheight);
		} else {
			$y = floor($x * $srcheight / $srcwidth);
		}
		$dstsize["width"] = $x;
		$dstsize["height"] = $y;
		return $dstsize;
	}


	/**
	 * 获取最后一条错误信息
	 * return string
	 */
	function GetLastError()
	{
		return array_pop($this->error);
	}

	/**
	 * 获取所有错误信息
	 * return array
	 */
	function GetAllError()
	{
		return $this->error;
	}
}