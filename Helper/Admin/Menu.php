<?php
/**
 * 后台右侧菜单
 * @author      xzs
 * @copyright   zol
 * @version     1.0
 * @datetime    2014-09-01
 */
class Helper_Admin_Menu extends Helper_Abstract {
    /**
     * 根据用户和pageNode获取右侧菜单
     */
    public static function getMenu($paramArr) {
        $options = array(
            'userid'   => '', #用户名
            'cateType' => '',
            'ctlName'  => ''
        );
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
        
        if ($userid) {
            //获取用户所有权限
            $roleNode = Helper_Dao::getCol(array(
                'dbName'        => "Db_Xhprof",        #数据库名
                'tblName'       => "api_admin_access",             #表名
                'cols'          => "nodeid",                   #列名
                'whereSql'      => " and userid = '{$userid}'",  #where条件
            ));
            
            if($roleNode){
                $roleNode = array_unique($roleNode);
                $nodeIdStr = implode(',', $roleNode);
            
                //获得所有节点信息
                $nodeArr = Helper_Dao::getRows(array(
                    'dbName'        =>  'Db_Xhprof',    #数据库名
                    'tblName'       =>  'api_admin_nodes',    #表名
                    'cols'          =>  '*',   #列名
                    'whereSql'      =>  ' and id in('.$nodeIdStr.')',
                ));
            
                $newData = array();
                if ($nodeArr) {
                    foreach ($nodeArr as $key => $value) {
                        if ($value['pid']) {
                            $newData[$value['pid']]['list'][] = $value;
                        } else {
                            $newData[$value['id']]['menu'] = $value;
                        }
                    }
                }
                $outStr = '';
            
                if($newData){
                    foreach ($newData as $key => $val){
                        $outStr .= "<li";
                        if(isset($val['list'])){
                            $openClass = $cateType==$val['menu']['node'] ? "pre-open" : "";
                            $outStr .= ' class="cm-submenu '.$openClass.'">';
                            $outStr .= '<a class="'.$val['menu']['icon'].'">'.$val['menu']['title'].'<span class="caret"></span></a>';
                            $outStr .= '<ul>';
                            foreach ($val['list'] as $subMenu){
                                $actClass = $subMenu['node']==$ctlName ? 'class="active"' : '';
                                $outStr .= '<li '.$actClass.'><a href="'.$subMenu['href'].'" '. (!empty($subMenu['target']) ? 'target="'.$subMenu['target'].'"' : '') .'>'.$subMenu['title'].'</a></li>';
                            }
                            $outStr .= '</ul>';
                        }else{
                            $actClass = $val['menu']['node']==$ctlName ? ' class="active" ' : '';
                            $outStr .=  $actClass.'>';
                            $outStr .= '<a href="'.$val['menu']['href'].'" class="'.$val['menu']['icon'].'">'.$val['menu']['title'].'</a>';
                        }
                        $outStr .= "</li>";
                    }
                }
            
                return $outStr;
            }
        }
        
        return false;
    }
    
    /**
    * 获取权限
    * @date: 2018年12月5日 下午8:11:48
    * @author: dell
    * @param: variable
    * @return:
    */
    public static function accessVerify($paramArr) {
        $options = array(
            'userid' => '', #用户名
            'module' => 0
        );
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);

        if (!$userid || !$module) {
            return false;
        }

        //获取用户所有权限
        $roleNode = Helper_Dao::getCol(array(
            'dbName'        => "Db_Xhprof",        #数据库名
            'tblName'       => "api_admin_access",             #表名
            'cols'          => "nodeid",                   #列名
            'whereSql'      => " and userid = '{$userid}'",  #where条件
        ));

        return $roleNode ? in_array($module, $roleNode) : false;
    }
}