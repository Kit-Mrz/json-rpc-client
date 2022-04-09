<?php

namespace Mrzkit\JsonRpcClient\Contracts;

interface RpcServiceContract
{
    public function reconnect();

    public function connect();

    public function read();

    public function write($param);

    public function close();
}
