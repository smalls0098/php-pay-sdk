<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Wechat;

use Smalls\Pay\Events;
use Smalls\Pay\Exception\BusinessException;
use Smalls\Pay\Exception\GatewayException;
use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Exception\InvalidSignException;
use Smalls\Pay\Gateways\WeChat;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Config;
use Smalls\Pay\Supports\Log;
use Smalls\Pay\Supports\Str;
use Smalls\Pay\Supports\Xml;
use Smalls\Pay\Traits\HttpRequest;


/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:16
 **/
class Support
{
    use HttpRequest;

    private static $instance;

    private $baseUri;
    /**
     * @var Config
     */
    private $config;

    private $httpOptions;

    private function __construct(Config $config)
    {
        $this->baseUri = Wechat::URL[$config->get('mode', Wechat::MODE_NORMAL)];
        $this->config = $config;

        $this->setHttpOptions();
    }


    public function __get($key)
    {
        return $this->getConfig($key);
    }

    public static function create(Config $config)
    {
        if ('cli' === php_sapi_name() || !(self::$instance instanceof self)) {
            self::$instance = new self($config);

            self::setDevKey();
        }

        return self::$instance;
    }


    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            throw new InvalidArgumentException('You Should [Create] First Before Using');
        }

        return self::$instance;
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }


    public static function requestApi($endpoint, $data, $cert = false): Collection
    {

        Events::dispatch(new Events\ApiRequesting('Wechat', '', self::$instance->getBaseUri() . $endpoint, $data));
        $cert ? [
            'cert' => self::$instance->cert_client,
            'ssl_key' => self::$instance->cert_key,
        ] : [];
        $result = self::$instance->post(
            $endpoint,
            Xml::toXml($data),
            $cert
        );
        $result = is_array($result) ? $result : Xml::fromXml($result);
        Events::dispatch(new Events\ApiRequested('Wechat', '', self::$instance->getBaseUri() . $endpoint, $result));
        return self::processingApiResult($endpoint, $result);
    }


    protected static function processingApiResult($endpoint, array $result)
    {
        if (!isset($result['return_code']) || 'SUCCESS' != $result['return_code']) {
            throw new GatewayException('Get WeChat API Error:' . ($result['return_msg'] ?? $result['retmsg'] ?? ''));
        }

        if (isset($result['result_code']) && 'SUCCESS' != $result['result_code']) {
            throw new BusinessException('WeChat Business Error: ' . $result['err_code'] . ' - ' . $result['err_code_des']);
        }

        if ('pay/getsignkey' === $endpoint || false !== strpos($endpoint, 'mmpaymkttransfers') || self::generateSign($result) === $result['sign']) {
            return new Collection($result);
        }
        Events::dispatch(new Events\SignFailed('Wechat', '', $result));
        throw new InvalidSignException('WeChat Sign Verify FAILED');
    }

    public static function generateSign($data): string
    {
        $key = self::$instance->key;
        if (is_null($key)) {
            throw new InvalidArgumentException('Missing WeChat Config -- [key]');
        }
        ksort($data);
        $string = md5(self::getSignContent($data) . '&key=' . $key);
        Log::debug('WeChat Generate Sign Before UPPER', [$data, $string]);
        return strtoupper($string);
    }


    public function getConfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config->all();
        }
        if ($this->config->has($key)) {
            return $this->config[$key];
        }
        return $default;
    }


    public static function getSignContent($data): string
    {
        $buff = '';

        foreach ($data as $k => $v) {
            $buff .= ('sign' != $k && '' != $v && !is_array($v)) ? $k . '=' . $v . '&' : '';
        }

        Log::debug('Wechat Generate Sign Content Before Trim', [$data, $buff]);

        return trim($buff, '&');
    }


    private static function setDevKey()
    {

        if (Wechat::MODE_DEV == self::$instance->mode) {
            $data = [
                'mch_id' => self::$instance->mch_id,
                'nonce_str' => Str::random(),
            ];
            $data['sign'] = self::generateSign($data);

            $result = self::requestApi('pay/getsignkey', $data);

            self::$instance->config->set('key', $result['sandbox_signkey']);
        }

        return self::$instance;
    }


    private function setHttpOptions(): self
    {
        if ($this->config->has('http') && is_array($this->config->get('http'))) {
            $this->config->forget('http.base_uri');
            $this->httpOptions = $this->config->get('http');
        }
        return $this;
    }

    public static function decryptRefundContents($contents): string
    {
        return openssl_decrypt(
            base64_decode($contents),
            'AES-256-ECB',
            md5(self::$instance->key),
            OPENSSL_RAW_DATA
        );
    }

    public static function filterPayload($payload, $params, $preserve_notify_url = false): array
    {
        $type = self::getTypeName($params['type'] ?? '');

        $payload = array_merge(
            $payload,
            is_array($params) ? $params : ['out_trade_no' => $params]
        );
        $payload['appid'] = self::$instance->getConfig($type, '');

        unset($payload['trade_type'], $payload['type']);
        if (!$preserve_notify_url) {
            unset($payload['notify_url']);
        }

        $payload['sign'] = self::generateSign($payload);

        return $payload;
    }


    public static function getTypeName($type = ''): string
    {
        switch ($type) {
            case '':
                $type = 'app_id';
                break;
            case 'app':
                $type = 'appid';
                break;
            default:
                $type = $type . '_id';
        }

        return $type;
    }

}