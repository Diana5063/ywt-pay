<?php

namespace Application\Controller;

use Application\Common\BaseController;
use Application\Common\YwtPay;

class IndexController extends BaseController
{
    /**
     * some test
     * @return bool
     */
    public function indexAction()
    {
        echo 'index';
        $ywt_config = $this->getConfigAsArray()['ywt'];
        $ywt = new YwtPay($ywt_config, []);
        $form_arr = $ywt->getSingJson();
        //var_dump($form_arr);

        $requery_data = [
            'Head' => [
                'BranchNo' => $ywt_config['BranchID'],
                'MerchantNo' => $ywt_config['Cono'],
                'TimeStamp' => $ywt->getMicroTime(),//当前时间戳
                'Command' => 'QuerySingleOrder'
            ],
            'Body' => [
                'Date' => date('Ymd'),
                'BillNo' => '1000000001'
            ]
        ];
        $requery_xml = $ywt->getRequestXml($requery_data);
        //var_dump($requery_data);
        return false;
    }
}
