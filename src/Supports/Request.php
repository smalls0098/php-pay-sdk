<?php
declare (strict_types=1);

namespace Smalls\Pay\Supports;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 19:01
 **/
class Request
{


    /**
     * 当前请求的IP地址
     * @var string
     */
    protected static $realIP;

    /**
     * 当前SERVER参数
     * @var array
     */
    protected static $server = [];

    /**
     * 前端代理服务器IP
     * @var array
     */
    protected static $proxyServerIp = [];

    /**
     * 前端代理服务器真实IP头
     * @var array
     */
    protected static $proxyServerIpHeader = ['HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP'];


    /**
     * 获取客户端IP地址
     * @access public
     * @return string
     */
    public static function ip(): string
    {
        if (!empty(self::$realIP)) {
            return self::$realIP;
        }

        self::$realIP = self::server('REMOTE_ADDR', '');

        // 如果指定了前端代理服务器IP以及其会发送的IP头
        // 则尝试获取前端代理服务器发送过来的真实IP
        $proxyIp = self::$proxyServerIp;
        $proxyIpHeader = self::$proxyServerIpHeader;

        if (count($proxyIp) > 0 && count($proxyIpHeader) > 0) {
            // 从指定的HTTP头中依次尝试获取IP地址
            // 直到获取到一个合法的IP地址
            foreach ($proxyIpHeader as $header) {
                $tempIP = self::server($header);

                if (empty($tempIP)) {
                    continue;
                }

                $tempIP = trim(explode(',', $tempIP)[0]);

                if (!self::isValidIP($tempIP)) {
                    $tempIP = null;
                } else {
                    break;
                }
            }

            // tempIP不为空，说明获取到了一个IP地址
            // 这时我们检查 REMOTE_ADDR 是不是指定的前端代理服务器之一
            // 如果是的话说明该 IP头 是由前端代理服务器设置的
            // 否则则是伪装的
            if (!empty($tempIP)) {
                $realIPBin = self::ip2bin(self::$realIP);

                foreach ($proxyIp as $ip) {
                    $serverIPElements = explode('/', $ip);
                    $serverIP = $serverIPElements[0];
                    $serverIPPrefix = $serverIPElements[1] ?? 128;
                    $serverIPBin = self::ip2bin($serverIP);

                    // IP类型不符
                    if (strlen($realIPBin) !== strlen($serverIPBin)) {
                        continue;
                    }

                    if (strncmp($realIPBin, $serverIPBin, (int)$serverIPPrefix) === 0) {
                        self::$realIP = $tempIP;
                        break;
                    }
                }
            }
        }

        if (!self::isValidIP(self::$realIP)) {
            self::$realIP = '0.0.0.0';
        }

        return self::$realIP;
    }


    /**
     * 获取server参数
     * @access public
     * @param string $name 数据名称
     * @param string $default 默认值
     * @return mixed
     */
    public static function server(string $name = '', string $default = '')
    {
        if (empty($name)) {
            return self::$server;
        } else {
            $name = strtoupper($name);
        }

        return self::$server[$name] ?? $default;
    }


    /**
     * 检测是否是合法的IP地址
     *
     * @param string $ip IP地址
     * @param string $type IP地址类型 (ipv4, ipv6)
     *
     * @return boolean
     */
    public static function isValidIP(string $ip, string $type = ''): bool
    {
        switch (strtolower($type)) {
            case 'ipv4':
                $flag = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $flag = FILTER_FLAG_IPV6;
                break;
            default:
                $flag = null;
                break;
        }

        return boolval(filter_var($ip, FILTER_VALIDATE_IP, $flag));
    }


    /**
     * 将IP地址转换为二进制字符串
     *
     * @param string $ip
     *
     * @return string
     */
    public static function ip2bin(string $ip): string
    {
        if (self::isValidIP($ip, 'ipv6')) {
            $IPHex = str_split(bin2hex(inet_pton($ip)), 4);
            foreach ($IPHex as $key => $value) {
                $IPHex[$key] = intval($value, 16);
            }
            $IPBin = vsprintf('%016b%016b%016b%016b%016b%016b%016b%016b', $IPHex);
        } else {
            $IPHex = str_split(bin2hex(inet_pton($ip)), 2);
            foreach ($IPHex as $key => $value) {
                $IPHex[$key] = intval($value, 16);
            }
            $IPBin = vsprintf('%08b%08b%08b%08b', $IPHex);
        }

        return $IPBin;
    }

}