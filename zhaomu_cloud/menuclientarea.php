<?php
/*
 *  前台自定义菜单
 */
return [
    [
        'name' => '云服务器选购', # 菜单名称 默认为一级菜单
        'url'  => 'ZhaomuCloud://Index/index', # 菜单路由 (若有子菜单,此值留空)
        'fa_icon' => 'bx bxs-grid-alt', # 菜单图标 支持bootstrap
        'lang' => [ # 菜单多语言
            'chinese' => '云服务器选购', # 中文
            'chinese_tw' => '云服务器选购', # 台湾
            'english' => 'Cloud Server', # 英文
        ]    
    ]
];