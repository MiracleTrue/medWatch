<?php

namespace App\Sockets;

use Hhxsv5\LaravelS\Swoole\Socket\TcpSocket;
use Swoole\Server;

class WatchTcpSocket extends TcpSocket
{
    public function onConnect(Server $server, $fd, $reactorId)
    {

        //


        \Log::info('New TCP connection', [$fd]);
        $server->send($fd, 'Welcome to WatchTcpSocket.');
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        \Log::info('Close WatchTcpSocket connection', [$fd]);
        $server->send($fd, 'Goodbye');
    }


    public function onReceive(Server $server, $fd, $reactorId, $data)
    {
        // 首次开机 收到60201 imei & imsi

        // 二次开机 收到60102 设备开关机通知


        $command = 60201;

        


        \Log::info('WatchTcpSocket  Received data', [$fd, $reactorId, $data]);


        $server->send($fd, 'LaravelS: ' . $data);


        if ($data === "quit\r\n")
        {
            $server->send($fd, 'LaravelS: bye' . PHP_EOL);
            $server->close($fd);
        }
    }

}