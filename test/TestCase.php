<?php

namespace Mrzkit\JsonRpcClient\Test;

use Mrzkit\JsonRpcClient\RpcService\JsonRpcSocketClient;

class TestCase
{
    public static function testRun()
    {
        try {
            $jsonRpcClient = new JsonRpcStreamClient();
            $jsonRpcClient->connect();

            $call = [
                'jsonrpc' => '2.0',
                'method'  => '/calculator/add',
                'params'  => [
                    800, 900
                ],
                'id'      => uniqid(),
                'context' => [],
            ];
            $content = $jsonRpcClient->waitCall($call);
            var_dump($content);


            $call = [
                'jsonrpc' => '2.0',
                'method'  => '/customer/getCustomer',
                'params'  => [
                    110,
                ],
                'id'      => uniqid(),
                'context' => [],
            ];
            $content = $jsonRpcClient->waitCall($call);
            var_dump($content);


        } catch (JsonRpcClientException $e) {
            echo $e->getMessage() . "\r\n";
        }
    }

    public static function testJsonRpcSocketClient()
    {
        $jsonRpcSocketClient = new JsonRpcSocketClient(
            [
                'host' => '127.0.0.1',
                'port' => '9502',
                'package_length_type' => 'N',
                'package_length_offset' => 0,
                'package_body_offset' => 4,
            ]
        );

        $jsonRpcSocketClient->connect();

        $params = [
            'jsonrpc' => '2.0',
            'method' => '/calculator/add',
            'params' => [100, 2300],
            'id' => '5fd240b637539',
            'context' => [],
        ];

        $content = $jsonRpcSocketClient->waitCall($params);
        var_dump($content);

        $params = [
            'jsonrpc' => '2.0',
            'method' => '/customer/getCustomer',
            'params' => [110],
            'id' => '5fd240b637539',
            'context' => [],
        ];

        $content = $jsonRpcSocketClient->waitCall($params);
        var_dump($content);

        $jsonRpcSocketClient->close();
    }
}
