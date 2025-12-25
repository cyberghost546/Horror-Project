<?php
require __DIR__ . '/vendor/autoload.php';


use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface
{

    protected $clients;
    protected $users = [];
    protected $activity = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $minute = date('H:i');

        if (!isset($this->activity[$minute])) {
            $this->activity[$minute] = 0;
        }
        $this->activity[$minute]++;
        $this->broadcastOnline();


        if ($data['type'] === 'chat') {
            if (isset($this->users[$from->resourceId])) {
                $this->users[$from->resourceId]['chatting'] = true;
                $this->users[$from->resourceId]['last_seen'] = time();
                $this->broadcastOnline();
            }
        }

        $this->users[$from->resourceId] = [
            'username' => $data['username'],
            'role' => $data['role'],
            'chatting' => false,
            'last_seen' => time()
        ];



        foreach ($this->clients as $client) {
            $client->send(json_encode($data));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (isset($this->users[$conn->resourceId])) {
            unset($this->users[$conn->resourceId]);
            $this->broadcastOnline();
        }

        $this->clients->detach($conn);
    }

    protected function broadcastOnline()
    {
        $payload = [
            'type' => 'online',
            'users' => array_values($this->users)
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($payload));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

IoServer::factory(
    new HttpServer(new WsServer(new ChatServer())),
    8080
)->run();

