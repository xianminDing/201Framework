<?php

return array(
    '首页'    => array(
        'cateType' => 'Index',
        'isSub' => 0,
        'icon'  => 'sf-house',
        'ctrl'  => 'Default', 
        'href'  => '/',
    ),
    '公共功能' => array(
        'cateType' => 'Pub',
        'isSub' => 1,
        'icon'  => 'sf-brick',
        'href'  => '',
        'subMenu' => array(
            array(
                'text' => '非法词汇管理',
                'href' => '/?c=IllWord',
                'ctrl' => 'IllWord',
                'target' => '',
            ),
        ),
    ),
);