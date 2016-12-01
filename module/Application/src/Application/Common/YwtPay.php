<?php

namespace Application\Common;
use phpseclib\Crypt\RC4;

class YwtPay
{
    public static $to_enc = 'GB2312';//转换后的编码
    public static $from_enc = 'UTF-8';//原编码
    public $config;
    public $BillNo;
    public $Amount;
    public $MerchantUrl;
    public $MerchantPara;//商户会在支付通知结果中获取到这个参数 一般是由数组拼接成一个字符串
    public $MerchantRetUrl;//支付完成之后跳转的地址
    public $MerchantRetPara;//支付完成后跳转的链接会带上这个参数;
    public $Date;
    public $PayerID;
    public $PayeeID;
    public $ClientIP;
    public $GoodsType;
    public $Reserved;

    public function __construct($config, $params)
    {
        $this->config = $config;
        $this->BillNo = array_key_exists('BillNo', $params) ? $params['BillNo'] : '';
        $this->Amount = array_key_exists('Amount', $params) ? $params['Amount'] : '';
        $this->MerchantPara = array_key_exists('MerchantPara', $params) ? $this->getMerchantParaString($params['MerchantPara']) : '';
        $this->MerchantUrl = array_key_exists('MerchantUrl', $params) ? $params['MerchantUrl'] : '';
        $this->MerchantRetPara = array_key_exists('MerchantRetPara',$params) ? $this->getMerchantRetParaString($params['MerchantRetPara']) : '';
        $this->MerchantRetUrl = array_key_exists('MerchantRetUrl', $params) ? $params['MerchantRetUrl'] : '';
        $this->Date = date('Ymd');
        $this->PayerID = array_key_exists('PayerID', $params) ? $params['PayerID'] : '';
        $this->PayeeID = array_key_exists('PayeeID', $params) ? $params['PayeeID'] : '';
        $this->ClientIP = array_key_exists('ClientIP', $params) ? $params['ClientIP'] : '';
        $this->GoodsType = array_key_exists('GoodsType', $config) ? $config['GoodsType'] : '';
        $this->Reserved = array_key_exists('Reserved', $params) ? $this->getStrReserved($params['Reserved']) : '';
        $this->payUrl = $config['pay_url'];
    }

    //生成APP支付所需要的字符串,, 由参数拼接而成,,, 客户端使用GET方式发起请求
    public function getSingStr()
    {}

    /**
     * 获取 发起支付请求所需要的参数
     * @return array
     */
    public function getSingJson()
    {
        //发起支付请求所需要的参数
        $params = [
            'BranchID' => $this->config['BranchID'],
            'CoNo' => $this->config['Cono'],
            'BillNo' => $this->BillNo,
            'Amount' => $this->Amount,
            'Date' => $this->Date,
            'MerchantUrl' => $this->MerchantUrl,
            'MerchantPara' => $this->MerchantPara,
            'MerchantRetUrl' => $this->MerchantRetUrl,
            'MerchantRetPara' => $this->MerchantRetPara,
            'MerchantCode' => $this->getMerchantCode(),
            'pay_url' => $this->config['pay_url']
        ];

        //组成完成的请求链接　
        $params1 = $params;
        $url = $params1['pay_url'];
        //unset($params1['pay_url']);
        //$query_params = http_build_query($params1);
        $query_params = 'BranchID=' . $params['BranchID'] . '&CoNo=' . $params['CoNo'] . '&BillNo=' . $params['BillNo']
            . '&Amount=' . $params['Amount'] . '&Date=' . $params['Date'] . '&MerchantUrl=' . $params['MerchantUrl']
            . '&MerchantPara=' . $params['MerchantPara'] . '&MerchantRetUrl=' . $params['MerchantRetUrl'] . '&MerchantRetPara='
            . $params['MerchantRetPara'] . '&MerchantCode=' . $params['MerchantCode'];
        $url .= '?' . $query_params;
        $params['url'] = $url; // 完整的请求链接
        return $params;
    }

    /**
     * 获取校验码
     * @return string
     */
    public function getMerchantCode()
    {
        return static::genMerchantCode($this->config['strKey'], $this->Date, $this->config['BranchID'],
            $this->config['Cono'], $this->BillNo, $this->Amount, $this->MerchantPara, $this->MerchantUrl,
            $this->PayerID, $this->PayeeID, $this->ClientIP, $this->GoodsType, $this->Reserved);
    }

    /**
     * 生成支付校验码
     * @param $var0 string 商户密钥
     * @param $var1 string 订单日期
     * @param $var2 string 商户开户的分行号
     * @param $var3 string 商户号
     * @param $var4 string 订单号
     * @param $var5 float 订单金额
     * @param $var6 string 商户自定义参数
     * @param $var7 string 商户接受支付结果通知的URL
     * @param $var8 string 付款方用户标识
     * @param $var9 string 收款方用户标识
     * @param $var10 string 商户取得的客户端IP
     * @param $var11 string 商品类型
     * @param $var12 string 保留字段
     * @return string
     */
    public static function genMerchantCode(
        $var0,
        $var1,
        $var2,
        $var3,
        $var4,
        $var5,
        $var6,
        $var7,
        $var8,
        $var9,
        $var10 = '',
        $var11 = '',
        $var12 = ''
    ) {
        $var13 = '';
        if (strlen($var10) > 0) {
            $var13 .= "<\$ClientIP\$>" . $var10 . "</\$ClientIP\$>";
        }

        if (strlen($var11) > 0) {
            $var13 .= "<\$GoodsType\$>" . $var11 . "</\$GoodsType\$>";
        }

        if (strlen($var12) > 0) {
            $var13 .= "<\$Reserved\$>" . $var12 . "</\$Reserved\$>";
        }
        $var14 = static::combine($var0, $var8, $var9, $var13);
        $varz15 = $var0 . $var14 . $var1 . $var2 . $var3 . $var4 . $var5 . $var6 . $var7;
        return '|' . $var14 . '|' . sha1(mb_convert_encoding($varz15, static::$to_enc, static::$from_enc));
    }

    public static function combine($var0, $var1, $var2, $var3 = '')
    {
        $var4 = static::getMathRandom() . '|' . $var1 . "<\$CmbSplitter\$>" . $var2 . $var3;
        $var5 = md5($var0, true);
        $rc4 = new RC4();
        $rc4->setKey($var5);
        $var13 = $rc4->encrypt(mb_convert_encoding($var4, static::$to_enc, static::$from_enc));
        return str_replace('+', '*', base64_encode($var13));
    }

    /**
     * 组装商户校验码中的保留字段字符串
     * @param $arr
     * @return string
     */
    public function getStrReserved($arr)
    {
        $str = '<Protocol>';
        //以下字段必填
        $str .= '<PNo>' . $arr['PNo'] . '</PNo>';
        $str .= '<TS>' . $arr['TS'] . '</TS>';
        $str .= '<MchNo>' . $arr['MchNo'] . '</MchNo>';

        //以下字段 签约+支付 时必填
        if (!empty($arr['Seq'])) {
            $str .= '<Seq>' . $arr['Seq'] . '</Seq>';
        }
        if (!empty($arr['URL'])) {
            $str .= '<URL>' . $arr['URL'] . '</URL>';
        }

        //以下字段选填
        if (!empty($arr['Para'])) {
            $str .= '<Para>' . $arr['Para'] . '</Para>';
        }
        if (!empty($arr['MUID'])) {
            $str .= '<MUID>' . $arr['MUID'] . '</MUID>';
        }
        if (!empty($arr['Mobile'])) {
            $str .= '<Mobile>' . $arr['Mobile'] . '</Mobile>';
        }
        if (!empty($arr['LBS'])) {
            $str .= '<LBS>' . $arr['LBS'] . '</LBS>';
        }
        if (!empty($arr['RskLvl'])) {
            $str .= '<RskLvl>' . $arr['RskLvl'] . '</RskLvl>';
        }
        $str .= '</Protocol>';
        return $str;
    }

    public function getMerchantParaString($params)
    {
        return $this->arrToString($params);
    }

    public function getMerchantRetParaString($params)
    {
        return $this->arrToString($params);
    }

    /**
     * 将参数数组转换成带有符号 | 的字符串
     * @param $params
     * @return string
     */
    public function arrToString($params)
    {
        $str = '';
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $str .= $key . '=' . $value . '|';
            }
            $str = rtrim($str, '|');
        } else {
            $str = $params;
        }
        return $str;
    }

    /**
     * 返回一个(0,1)之间的小数，小数点后保留17位
     * @return string
     */
    public static function getMathRandom()
    {
        //return (string) '0.' . mt_rand('100000000000000', '9999999999999999');
        $r1 = sprintf('%.6f', mt_rand(0, 999999) / 1000000);
        $r2 = sprintf('%.6f', mt_rand(0, 999999) / 1000000);
        $r3 = sprintf('%.5f', mt_rand(0, 99999) / 100000);
        $r = $r1 . explode('.', $r2)[1] . explode('.', $r3)[1];
        return $r;
    }

    /**
     * 将xml转换成数组
     * @param $xml
     * @return mixed
     */
    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 将一维数据转换成xml格式
     * @param $arr
     * @return string
     */
    public function arrayToXml($arr)
    {
        $xml = '';
        foreach ($arr as $k => $v) {
            $xml .= '<' . $k . '>' . $v . '</' . $k . '>';
        }
        return $xml;
    }

    /**
     * 拼接订单查询需要的xml
     * @param $request_arr
     * @return string
     */
    public function getRequestXml($request_arr)
    {
        $xml = '<Request>';
        $str = $this->config['strKey'];

        if (is_array($request_arr)) {
            //拼接Head
            if (array_key_exists('Head', $request_arr)) {
                $xml .= '<Head>' . $this->arrayToXml($request_arr['Head']) . '</Head>';
                $str .= $this->arrayToXml($request_arr['Head']);
            }

            //拼接Body
            if (array_key_exists('Body', $request_arr)) {
                $xml .= '<Body>' . $this->arrayToXml($request_arr['Body']) . '</Body>';
                $str .= $this->arrayToXml($request_arr['Body']);
            }
        }

        //拼接签名
        $xml .= '<Hash>' . sha1($str) . '</Hash>';//招行系统定单查询的签名
        $xml .= '</Request>';
        return mb_convert_encoding($xml, static::$to_enc, static::$from_enc);
    }

    /**
     * 验证支付结果中的签名
     * @param $str
     * @return int 1 if the signature is correct, 0 if it is incorrect, and -1 on error
     */
    public function verifySignForPay($str)
    {
        $str = mb_convert_encoding($str, static::$to_enc, static::$from_enc);
        list($data, $tmpSignature) = explode('&Signature=', $str);
        $tts = explode('|', rtrim($tmpSignature, '|')); //移除最后一个|, 否则验证无法通过
        $signature = pack('C*', ...$tts); //把数字压成二进制串
        $publickey = file_get_contents($this->config['public_key_path']);//读取公钥文件
        return openssl_verify($data, $signature, $publickey);
    }


    /**
     * 验证签约结果中的签名
     * @param $req_data_arr
     * @return int 1 if the signature is correct, 0 if it is incorrect, and -1 on error
     */
    public function verifySignForSign($req_data_arr)
    {
        $sign_arr = [
            'NTBNBR' => $req_data_arr['NTBNBR'],
            'TRSCOD' => $req_data_arr['TRSCOD'],
            'COMMID' => $req_data_arr['COMMID'],
            'SIGTIM' => $req_data_arr['SIGTIM'],
            'BUSDAT' => $req_data_arr['BUSDAT'],
        ];
        //得到签名原文
        $sign_str = http_build_query($sign_arr);
        $public_key_path = $this->config['sign_public_key_path'];//获取公匙的路径
        $pubKey = file_get_contents($public_key_path);
        //$res = openssl_get_publickey($pubKey);
        $result = openssl_verify($sign_str, $req_data_arr['SIGDAT'], $pubKey);
        //openssl_free_key($res);
        return $result; //返回验签结果
    }

    public function verifySignForOrder($str)
    {
        //TODO 验证免登录接口订单处理相关的签名
    }

    /**
     * 获取当前时间戳，精确到毫秒
     * @return float
     */
    public function getMicroTime()
    {
        //1.北京时间，格式为整数。表示“从2000-1-1 00:00:00以来的“毫秒数”。例如：当前时间为2014-06-27 09:38:15，减去2000-1-1 00:00:00得到的时间差，换算成毫秒数：457177095000。
        //2.在实现的时候，应该采用64位的整数，如果采用32位整数会导致算数溢出。
        //3.银行收到报文的时候，会检查这个时间。如果这个“发起时间”和当前时间相差超过1小时，则请求不予处理。
        $now_datetime = date('Y-m-d H:i:s');
        $past_datetime = '2000-1-1 00:00:00';
        $micro_time = (strtotime($now_datetime) - strtotime($past_datetime)) * 1000;
        return $micro_time;
    }
}
