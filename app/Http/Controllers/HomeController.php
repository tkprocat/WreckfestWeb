<?php

namespace App\Http\Controllers;

use App\Services\WreckfestApiClient;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected WreckfestApiClient $apiClient;

    public function __construct(WreckfestApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function index()
    {
        $serverConfig = $this->apiClient->getServerConfig();
        $serverStatus = $this->apiClient->getServerStatus();
        $players = $this->apiClient->getPlayers();

        return view('home', [
            'serverName' => $serverConfig['serverName'] ?? 'Wreckfest Server',
            'serverStatus' => $serverStatus,
            'players' => $players,
            'maxPlayers' => $serverConfig['maxPlayers'] ?? 24,
        ]);
    }
}
