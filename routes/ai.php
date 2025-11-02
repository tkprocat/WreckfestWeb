<?php

use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/wreckfest', \App\Mcp\Servers\Wreckfest::class);
