<?php
/**
 * Created by PhpStorm.
 * User: diana
 * Date: 16-11-30
 * Time: 上午10:39
 */

$certPath = dirname($_SERVER['DOCUMENT_ROOT']) . '/security';

return [
    'ywt' => [
        'strKey' => '', //测试环境为空
        'BranchID' => '0027',//商户开户的分行号
        'Cono' => '000053',//商户号，6位数字
        'GoodsType' => '54011600',//商品类型，54011600表示网上支付
        'MchNo' => '',//企业网银编号，8位字符串（数字、字母混合）
        'sign_back_url' => '',//签约结果回调地址
        'public_key_path' => $certPath . '/public.pem',//支付结果验签公钥路径
        'sign_public_key_path' => $certPath . '/sign_public.pem',//签约结果验签公钥路径
        'private_key_path' => '',//私钥路径
        'pay_url' => 'http://61.144.248.29:801/netpayment/BaseHttp.dll?PrePayEUserP',//招行支付测试接口
        'order_query_url' => 'http://218.17.27.197/netpayment/BaseHttp.dll?DirectRequestX',//单笔订单查询、商户入账查询、已结账订单查询及退款接口
        'operator' => '9999',//招行免登录版订单处理的操作员
        'op_password' => '000018',//招行免登录版订单处理的操作密码
    ]
];
