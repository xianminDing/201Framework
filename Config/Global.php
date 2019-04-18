<?php
/**
 * 常用的全局变量(暂时只有高级搜索相关变量)
 * @author wanghb <wang.haobin@zol.com.cn>
 * @copyright(c)
 * @version v1.0 2010-10-15
 */
return array(
    'PAGE_TPL'        => '{PREV:&lt;上一页}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{NEXT:下一页&gt;}',
    'CJPAGE_TPL'      => '{PREV:<span class="pre">上一页</span>}{FIRST:<span>[NUM]</span>}{PREVHD:<span class="bgno">...</span>}{BAR:<span>[NUM]</span>:5:2}{NEXTHD:<span class="bgno">...</span>}{LAST:<span>[NUM]</span>}{NEXT:<span class="next">下一页&gt;</span>}',
    'CJLIST_TPL'      => '{PREV:<span class="pre">上一页</span>}{FIRST:<span>[NUM]</span>}{PREVHD:<span class="bgno">...</span>}{BAR:<span>[NUM]</span>:5:2}{NEXTHD:<span class="bgno">...</span>}{NEXT:<span class="next">下一页&gt;</span>}',
    'LISTPAGE_TPL'    => '{PREV:<span class="pre">上一页</span>}{FIRST:<span>[NUM]</span>}{PREVHD:...}{BAR:<span>[NUM]</span>:5:2}{NEXTHD:...}{NEXT:<span class="next">下一页</span>}',
    
    #返利类型
    'PROMOTION_TYPE' => array(
        1 => '无活动',
        2 => '返利',
        3 => '优惠券',
    ),
	'CASH_SECRET' => 'zolfighting' #返现项目秘钥
);
