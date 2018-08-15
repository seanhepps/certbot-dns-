<?php
return array(
    'dns'       =>  array(
        'aliyun'    =>  array(
            'url'   =>  'https://alidns.aliyuncs.com/',
            'key'   =>  'LTAItEVgA2NcDKpv',
            'secret'=>  '1qfMwX05I8Hor9S4WLeq4gEZGHvAs2',
            'method'=>  'get',
        ),
    ),
    'domain'    =>  array(
        'pjcy_cn'   =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.pjcy.cn')
        ),
        'test_pjcy_cn'   =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('test.pjcy.cn'),
            'rootDomain'    =>'pjcy.cn'
        ),
        'test1_pjcy_cn'   =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('test1.pjcy.cn'),
            'rootDomain'    =>'pjcy.cn'
        ),
        'test2_pjcy_cn'   =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('test2.pjcy.cn'),
            'rootDomain'    =>'pjcy.cn'
        ),
        'vippua_com'=>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.vippua.com'),
            'rootDomain'    =>'pjcy.cn'
        ),
        'puaok_com' =>  array(
            'dns'   =>  'aliyun',
            'cdnName'=> array('www.puaok.com')
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