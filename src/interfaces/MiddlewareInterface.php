<?php

namespace App\Interfaces;

use App\Http\Request;
use App\Http\Response;

interface MiddlewareInterface
{
	public function process(Request $request, Response $response): null|Response;
}