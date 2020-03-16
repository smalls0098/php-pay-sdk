<?php
declare (strict_types=1);

namespace Smalls\Pay\Traits;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:55
 **/
trait HttpRequest
{

    private $baseUri;
    private $timeout = 5.0;

    public function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query' => $query,
        ]);
    }

    public function post($endpoint, $data, $options = [])
    {
        if (!is_array($data)) {
            $options['body'] = $data;
        } else {
            $options['form_params'] = $data;
        }
        return $this->request('post', $endpoint, $options);
    }

    public function request($method, $endpoint, $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($endpoint, $options));
    }

    public function getBaseOptions()
    {
        $options = [
            'base_uri' => property_exists($this, 'baseUri') ? $this->baseUri : '',
            'timeout' => property_exists($this, 'timeout') ? $this->timeout : 5.0,
        ];
        return $options;
    }

    public function getHttpClient(array $options = [])
    {
        return new Client($options);
    }


    public function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
        }
        return $contents;
    }

}