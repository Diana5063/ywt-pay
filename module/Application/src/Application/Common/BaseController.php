<?php

namespace Application\Common;
use Zend\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController
{
    public $dbs = [];
    public $tables = [];
    public $config;

    /**
     * 格式化输出小数点后２位精度的浮点数
     * @param $v
     * @return string
     */
    public function floatdot2precise($v)
    {
        $strpos = strpos((string)$v, '.');
        if ($strpos !== false) {
            return sprintf('%.02f', substr($v, 0, $strpos + 3));
        }
        return sprintf('%.02f', $v);

    }

    public function getConfigAsArray()
    {
        if (null === $this->config) {
            $this->config = $this->getServiceLocator()->get('config');
        }
        return $this->config;
    }

    public function getDB($db = 'db')
    {
        if (!array_key_exists($db, $this->dbs)) {
            try {
                $this->dbs[$db] = $this->getServiceLocator()->get($db);
            } catch (\Exception $e) {
                var_export($e, true);
            }
        }
        return $this->dbs[$db];
    }

    public function isValidJson($str)
    {
        $pcre_regex = '
  /
  (?(DEFINE)
     (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
     (?<boolean>   true | false | null )
     (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
     (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
     (?<pair>      \s* (?&string) \s* : (?&json)  )
     (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
     (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
  )
  \A (?&json) \Z
  /six
';
        return (bool)preg_match($pcre_regex, $str);
    }

    /**
     * 301 永久重定向
     * @param $route
     * @param array $params
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function PermanentRedirectToRoute($route, $params = [])
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $this->plugin('url')->fromRoute($route, $params));
        $response->setStatusCode(301);
        return $response;
    }

    /**
     * 转换timestamp类型，格式化时间戳
     * @param $time
     * @return string
     */
    public function getTimestamp($time)
    {
        $d = new \DateTime();

        if (is_numeric($time)) {
            $d->setTimestamp($time);
        } else {
            $d->setTimestamp(strtotime($time));
        }
        return $d->format('Y-m-d H:i:s');
    }
}
