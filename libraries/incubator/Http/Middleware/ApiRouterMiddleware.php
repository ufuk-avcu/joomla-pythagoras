<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Joomla\Http\MiddlewareInterface;
use Joomla\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Determines the command to be executed.
 *
 * @package  Joomla/HTTP
 *
 * @since    1.0
 */
class ApiRouterMiddleware implements MiddlewareInterface
{
	/**
	 * Execute the middleware. Don't call this method directly; it is used by the `Application` internally.
	 *
	 * @internal
	 *
	 * @param   ServerRequestInterface $request  The request object
	 * @param   ResponseInterface      $response The response object
	 * @param   callable               $next     The next middleware handler
	 *
	 * @return  ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
	{
		$attributes = $request->getAttributes();

		if (!isset($attributes['command']))
		{
			switch (strtoupper($request->getMethod()))
			{
				case 'GET':
					$params = new Registry($request->getQueryParams());
					break;

				case 'POST':
				default:
					$params = new Registry($request->getAttributes());
					break;
			}

			$uri   = $request->getUri();
			$parts = array_filter(explode('/', $uri->getPath()));

			switch (count($parts))
			{
				case 1:
					// subject: A list of entities
					$action = 'List';
					$entity = $parts[0];
					$id     = null;
					break;

				case 2:
					// subject: A single entity by id
					$action = 'Display';
					$entity = $parts[0];
					$id     = $parts[1];
					break;

				case 3:
					// subject: A list of entities belonging to another
					$action = 'List';
					$entity = $parts[2];
					$id     = null;
					break;

				default:
					throw new \RuntimeException('Invalid request');
			}

			$component = ucfirst(strtolower($params->get('option', 'content')));

			$commandClass = "\\Joomla\\Component\\{$component}\\Command\\{$action}Command";

			if (class_exists($commandClass))
			{
				$command = new $commandClass($entity, $id, $response->getBody());
				$request = $request->withAttribute('command', $command);
			}
			// @todo Emit afterRouting event
		}

		return $next($request, $response);
	}
}
