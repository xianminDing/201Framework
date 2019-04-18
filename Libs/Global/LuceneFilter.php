<?php
/**
 * 产品库搜索2.0条件过滤文件，为了与search保持一致用的接口
 * add by wanghb on 2011-06-07
 */

class Libs_Global_LuceneFilter
{
    /**
	* @var ZOL_Product_Lib_ProductInfo
	*/
	private static $DB_Search;

    /*
     * 搜索服务提供地址
     */
	private static $new_search_machine = 'product.lucene.zol.com.cn';
	private static $new_search_port = '6036';

	/**
	* 初始化信息
	*/
	public static function loadDB()
	{
		self::$DB_Search = Db_Search::instance();
	}

	/**
	* 关键字过滤
	*/
	public static function getKeyword($kword)
	{
        self::loadDB();
        $s_price = '';
        $sub_id = 0;
        $manu_id = 0;
        $add_subcate_arr = array('gphone'=>'57','单反'=>'15','单反相机'=>'15');    //固定关键词指定类别
        $match_subcate_arr = array('镜头'=>268);   //匹配关键词指定类别
        
        //判断编码处理
        $str_code = mb_detect_encoding($kword);
        if ($str_code == 'UTF-8') {
            $newstr = mb_convert_encoding($kword,'GBK',$str_code);
            $k_en = preg_replace('#[a-z0-9\.,/\+]#isU','',$kword);
            $s_en = preg_replace('#[a-z0-9\.,/\+]#isU','',$newstr);
            if (trim($s_en) && strlen($s_en)*3 >= strlen($k_en)*2) {
                $kword = $newstr;
            }
        }
        
        $kword = trim($kword,'=/.');
        //关键词处理
        if (!$kword) {
            header('location:http://search.zol.com.cn/s/');
            exit();
        } else {
            if (preg_match("/^[a-zA-z]*$/", $kword)) {
                $kword = strtolower(ZOL_String::htmlSpecialChars($kword));
            }else{
                $kword = ZOL_String::htmlSpecialChars($kword);
            }
        }
        
        //价格区间处理
        if (preg_match('#(\d+)(元|块)?(到|至|\-)(\d+)(元|块)#sU',$kword,$match)) {
            $s_price = $match[1].'BBB'.$match[4];
            $kword = str_replace($match[1].$match[2].$match[3].$match[4].$match[5],'',$kword);
        } elseif (preg_match('#(\d+)(元|块)?以(下|内)#sU',$kword,$match)) {
            $s_price = '0BBB'.$match[1];
            $kword = str_replace($match[1].$match[2].'以'.$match[3],'',$kword);
        } elseif (preg_match('#(\d+)(元|块)?以上#sU',$kword,$match)) {
            $s_price = $match[1].'BBB0';
            $kword = str_replace($match[1].$match[2].'以上','',$kword);
        } elseif (preg_match('#(\d+)(元|块)#sU',$kword,$match)) {
            $s_price = intval($match[1]*0.9).'BBB'.intval($match[1]*1.1);
            $kword = str_replace($match[1].$match[2],'',$kword);
            $kword = str_replace('左右','',$kword);
        }

        if (array_key_exists(strtolower($kword),$add_subcate_arr)) {    //固定词指定类别处理
            $sub_id = $add_subcate_arr[$kword];
        }
        foreach ($match_subcate_arr as $mk=>$subcatid) {    //匹配词指定类别，仅限一个
            if (strpos($kword,$mk) !== false) {
                $sub_id = $subcatid;
                break;
            }
        }

        $se_kword = self::replace_kword($kword,1);
        //$se_kword = preg_replace('#([a-z]{3,})([0-9]+)#isU',"\\1#\\2",$se_kword);
        
        //判断初步纠错
        $sql = "select kword,type_id From note.catefirst Where kword={$se_kword} limit 1";
        $note_rows = Libs_Global_ReadSearchXml::readSearchXml($sql,self::$new_search_machine,self::$new_search_port);
        if (isset($note_rows['result attr']) && $note_rows['result attr']['hits'] >= 1) {
            $pg_str = preg_replace('#([\s]+)#sU','#',strtolower($note_rows['result']['row']['kword']));
            if ($pg_str != strtolower($se_kword)) {
                $note_word = $note_rows['result']['row']['kword'];
            }
        } else {
            $sword = str_replace(array('（','）','【','】','：','？','，'),' ',$kword);
            $sword = preg_replace('#([\s\(\)\[\]\*\?\,]+)#',' ',$sword);
            $sword = preg_replace('#([A-Z]+)#e','strtolower("\\1")',trim($sword));
            $sql = "select replace_word from z_keyword_list Where keyword='{$sword}'";
            $note_word = self::$DB_Search->getOne($sql);
        }
        
        $add_cond = '';
        $cond = '(title='.$se_kword.' or keyword='.$se_kword.')';
        
        if ($manu_id) {  //品牌条件筛选
            $add_cond .= " And manu_id=".$manu_id;
        }
        if ($sub_id) {     //类别筛选条件
            $add_cond .= " And sub_id=".$sub_id;
        }
        if (strstr($s_price,'BBB')) {   //价格区间搜索
            $pa = explode('BBB',$s_price);
            if ($pa[1] == 0) {
                $add_cond.= " And price>=".$pa[0]." ";
            } else if ($pa[0] == 0) {
                $add_cond .= " And price>0 And price<=".$pa[1];
            } else {
                sort($pa);
                $add_cond .= " And price>=".$pa[0]." And price<=".$pa[1];
            }
        }
        if ($s_price=='t') {
            $add_cond .= " And level>15 And price>0 order by price asc ";
        } else if ($s_price=='o') {
            $add_cond .= " And price=0 ";
        }

        #搜索提示
        $sql = "select id From product Where ".$cond.$add_cond.' limit 0';
        $rows = Libs_Global_ReadSearchXml::readSearchXml($sql,self::$new_search_machine,self::$new_search_port);
        
        if (isset($rows['result attr']) && $rows['result attr']['hits'] < 1 && $note_word) {
            return $note_word.'@@@(title='.$note_word.' or keyword='.$note_word.')'.$add_cond;
        } else if (isset($rows['result attr']) && $note_rows['result attr']['hits'] > 1
                && isset($note_rows['result']) && $note_rows['result']['row']['type_id'] < 4) {
            return $note_word.'@@@'.$cond.$add_cond;
        } else {
            return $cond.$add_cond;
        }
    }

    /**
     * 搜索关键词替换，提交数据的时候return带0，查询结果时return带1
     * @param string $str
     * @param 是否搜索替空掉 $return
     * @return 结果字符串
     */
    public static function replace_kword($str,$return=0) {
        $str = preg_replace('#([\s]+)#sU','#',$str);
        $str = preg_replace('#&(amp;)+#s','&',$str);
        if (!$return) {
            $rep_arr = array('价格','参数','报价','图片','下载','壁纸','新品','港行版','限量版','关键字');
            $str = str_replace($rep_arr,'',$str);
        }
        //匹配和替换的数组要一一对应，不然会替换出错
        $preg_match_arr = array('wcdma','td-scdma','cdma2000','P&E','B&W','WI-FI','ev-do','WM','Symbian^3');
        $preg_replace_arr = array('联通3G','移动3G','电信3G','PandE','BandW','WIFI','evdo','windows mobile','symbian3');
        return str_ireplace($preg_match_arr,$preg_replace_arr,$str);
    }

}


?>
