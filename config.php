<?php
return array(
    'dns'       =>  array(
        'aliyun'    =>  array(
            'url'   =>  'https://alidns.aliyuncs.com/',
            'key'   =>  'your key',
            'secret'=>  'your secret',
            'method'=>  'GET',
        ),
    ),
    'domain'    =>  array(
        'pjcy_cn'   =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.pjcy.cn')
        ),
        'vippua_com'=>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.vippua.com', 'wanhui.vippua.com'),
            'rootDomain'    =>'vippua_com'
        ),
        'puaok_com' =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.puaok.com', 'pd.puaok.com', 'love.puaok.com', 'm.puaok.com', 'i.puaok.com')
        ),
        'vippua_cn' =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.vippua.cn')
        ),
        '13xingfu_com'=>    array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.13xingfu.com')
        )
    ),
);