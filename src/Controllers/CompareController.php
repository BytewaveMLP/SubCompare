<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace SubCompare\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \ParagonIE\Certainty\RemoteFetch;
use \GuzzleHttp\Client as GuzzleClient;
use \GuzzleHttp\Exception\ClientException as GuzzleClientException;

class CompareController extends Controller {
	const CACHE_EXPIRE_AFTER = 60 * 60 * 6;

	const GUZZLE_DEFAULT_OPTIONS = [
		'http_errors' => true,
	];

	const PARAMETER_MISSING = "<b>Um...</b> Seems like you're missing user %d's ID.";
	const COULDNT_FIND_USER = "<b>Whoops!</b> Looks like we couldn't find user %d! Verify that the channel ID is correct, then try again.";
	const USER_SUBS_PRIVATE = "<b>Whoops!</b> Looks like user %d's subscriptions settings are set to <b>private</b>! They'll have to <a href=\"https://imgur.com/a/P6Dcm\" class=\"alert-link\">set their subscriptions to <b>public</b></a> before they can be compared here.";

	private $guzzle;
	private $guzzleParams;

	public function __construct($container) {
		parent::__construct($container);

		$fetcher = new RemoteFetch();
		$latestCACertBundle = $fetcher->getLatestBundle();
		$this->guzzle = new GuzzleClient();
		$this->guzzleParams = array_merge(self::GUZZLE_DEFAULT_OPTIONS, [
			'verify' => $latestCACertBundle->getFilePath(),
		]);
	}

	public function compare(Request $request, Response $response) {
		$user1 = $request->getParam('user1');
		$user2 = $request->getParam('user2');

		if (!$user1) {
			$this->container->flash->addMessage('danger', sprintf(self::PARAMETER_MISSING, 1));
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		if (!$user2) {
			$this->container->flash->addMessage('danger', sprintf(self::PARAMETER_MISSING, 2));
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		$user1 = trim($user1);
		$user2 = trim($user2);

		if (substr($user1, 0, 2) != 'UC') $user1 = 'UC' . $user1;
		if (substr($user2, 0, 2) != 'UC') $user2 = 'UC' . $user2;

		if ($user1 == $user2) {
			$this->container->flash->addMessage('warning', "<b>Hey!</b> Comparing the same channel would be meaningless!");
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		try {
			$user1Name = $this->getChannelNameFromID($user1);
		} catch (\Exception $e) {
			$this->container->flash->addMessage('danger', sprintf(self::COULDNT_FIND_USER, 1));
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		try {
			$user2Name = $this->getChannelNameFromID($user2);
		} catch (\Exception $e) {
			$this->container->flash->addMessage('danger', sprintf(self::COULDNT_FIND_USER, 2));
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		try {
			$user1Subs = $this->getUserSubscriptions($user1);
		} catch (GuzzleClientException $e) {
			$this->container->flash->addMessage('danger', sprintf(self::USER_SUBS_PRIVATE, 1));
			return $response->withRedirect($this->container->router->pathFor('home'));
		}
		
		try {
			$user2Subs = $this->getUserSubscriptions($user2);
		} catch (GuzzleClientException $e) {
			$this->container->flash->addMessage('danger', sprintf(self::COULDNT_FIND_USER, 2));
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		$commonSubs = array_intersect_key($user1Subs, $user2Subs);

		$user1SubsUnique = array_diff_key($user1Subs, $user2Subs);
		$user2SubsUnique = array_diff_key($user2Subs, $user1Subs);

		return $this->container->view->render($response, 'pages/compare.twig', [
			'names' => [
				'user1' => $user1Name,
				'user2' => $user2Name,
			],
			'ids' => [
				'user1' => $user1,
				'user2' => $user2,
			],
			'common' => $commonSubs,
			'unique' => [
				'user1' => $user1SubsUnique,
				'user2' => $user2SubsUnique,
			],
		]);
	}

	private function getChannelNameFromID($channelID) {
		$cacheName = hash('sha256', "CHANNEL-NAME-$channelID");
		if ($this->container->cache->hasItem($cacheName) && $this->container->cache->getItem($cacheName)->isHit()) {
			return $this->container->cache->getItem($cacheName)->get();
		} else {
			$params = array_merge($this->guzzleParams, [
				'query' => [
					'part' => 'snippet',
					'id' => $channelID,
					'key' => getenv('YT_API_KEY'),
				],
			]);

			$response = $this->guzzle->get('https://www.googleapis.com/youtube/v3/channels', $params);

			if ($response->getStatusCode() == 200) {
				$body = json_decode((string) $response->getBody(), true);

				if (!isset($body['items'][0])) throw new \Exception("Channel not found!");

				$title = $body['items'][0]['snippet']['title'];

				$item = $this->container->cache->getItem($cacheName);
				$item->set($title);
				$item->expiresAfter(self::CACHE_EXPIRE_AFTER);
				$this->container->cache->save($item);

				return $title;
			}
		}
	}

	private function getSubscriptionsOnPage($channelID, $page = null) {
		$cacheName = "SUBS-$channelID";
		if ($page) $cacheName .= "-PAGE-$page";

		$cacheName = hash('sha256', $cacheName);

		if ($this->container->cache->hasItem($cacheName) && $this->container->cache->getItem($cacheName)->isHit()) {
			return $this->container->cache->getItem($cacheName)->get();
		} else {
			$params = array_merge($this->guzzleParams, [
				'query' => [
					'part' => 'snippet',
					'channelId' => $channelID,
					'maxResults' => 50,
					'key' => getenv('YT_API_KEY'),
				],
			]);
			
			if ($page) {
				$params['query']['pageToken'] = $page;
			}

			$response = $this->guzzle->get('https://www.googleapis.com/youtube/v3/subscriptions', $params);
			if ($response->getStatusCode() == 200) {
				$body = json_decode((string) $response->getBody(), true);

				$ret = ['subscriptions' => $body['items']];

				if (isset($body['nextPageToken'])) {
					$ret['nextPage'] = $body['nextPageToken'];
				}

				$item = $this->container->cache->getItem($cacheName);
				$item->set($ret);
				$item->expiresAfter(self::CACHE_EXPIRE_AFTER);
				$this->container->cache->save($item);

				return $ret;
			}
		}
	}

	private function getUserSubscriptions($channelID) {
		$cacheName = hash('sha256', "SUBS-ARR-$channelID");
		if ($this->container->cache->hasItem($cacheName) && $this->container->cache->getItem($cacheName)->isHit()) {
			return $this->container->cache->getItem($cacheName)->get();
		} else {
			$subs = [];

			$response = $this->getSubscriptionsOnPage($channelID);
			$subs = array_merge($subs, $response['subscriptions']);

			while (isset($response['nextPage'])) {
				$response = $this->getSubscriptionsOnPage($channelID, $response['nextPage']);
				$subs = array_merge($subs, $response['subscriptions']);
			}

			$map = $this->convertSubsArrayToMap($subs);

			$item = $this->container->cache->getItem($cacheName);
			$item->set($map);
			$item->expiresAfter(self::CACHE_EXPIRE_AFTER);
			$this->container->cache->save($item);

			return $map;
		}
	}

	private function convertSubsArrayToMap($subs) {
		$converted = [];

		foreach ($subs as $sub) {
			$snippet = $sub['snippet'];
			$converted[$snippet['resourceId']['channelId']] = [
				'title' => $snippet['title'],
				'description' => $snippet['description'],
				'thumbnails' => $snippet['thumbnails'],
			];
		}

		return $converted;
	}
}
