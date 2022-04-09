<?php

namespace Mrzkit\JsonRpcClient\RpcService;

use Mrzkit\JsonRpcClient\Exceptions\RpcServiceException;

class JsonRpcStreamClient
{
    protected $socketClient;

    public function __construct()
    {
        if ( !extension_loaded('sockets')) {
            throw new RpcServiceException("Missing sockets extension.");
        }
    }

    protected function getConfig()
    {
        $conf = [
            'host'           => '127.0.0.1',
            'port'           => '9502',
            'socket_timeout' => 1,
            'flags'          => STREAM_CLIENT_CONNECT,
        ];

        return $conf;
    }

    protected function getAddress($host, $port)
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $address = "tcp://{$host}:{$port}";
        } else {
            $address = "tcp://[{$host}]:{$port}";
        }

        return $address;
    }

    public function getSocketClient()
    {
        if (is_null($this->socketClient)) {
            throw new JsonRpcClientException('Please connect socket.');
        }

        return $this->socketClient;
    }

    public function connect()
    {
        $config = $this->getConfig();

        $address = $this->getAddress($config['host'], $config['port']);

        $this->socketClient = stream_socket_client($address, $errno, $errstr, $config['socket_timeout'], $config['flags']);

        if ($errno != 0) {
            throw new JsonRpcClientException($errstr, $errno);
        }
    }

    protected function recv()
    {
        $content = fread($this->getSocketClient(), 8192);

        return $this->decode($content);
    }

    public function waitCall(array $pkg)
    {
        $pkg = $this->encode($pkg);

        $written = fwrite($this->getSocketClient(), $pkg . "\r\n");

        if ($written === false) {
            throw new JsonRpcClientException('fwrite error');
        }

        return $this->recv();
    }

    public function encode($pkg)
    {
        $pkg = json_encode($pkg, JSON_UNESCAPED_UNICODE);

        if (json_last_error()) {
            throw new JsonRpcClientException(json_last_error_msg());
        }

        return $pkg;
    }

    public function decode($pkg)
    {
        $pkg = json_decode($pkg, true);

        if (json_last_error()) {
            throw new JsonRpcClientException(json_last_error_msg());
        }

        return $pkg;
    }
}
