<?php

namespace dface\GmsNotify;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Log\LoggerInterface;

class GmsSender {

	/** @var ClientInterface */
	private $httpClient;
	/** @var ServerRequestFactoryInterface */
	private $requestFactory;
	/** @var callable */
	private $stringStreamFactory;
	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		ClientInterface $httpClient,
		ServerRequestFactoryInterface $requestFactory,
		LoggerInterface $logger,
		callable $stringStreamFactory
	) {
		$this->httpClient = $httpClient;
		$this->requestFactory = $requestFactory;
		$this->logger = $logger;
		$this->stringStreamFactory = $stringStreamFactory;
	}

	/**
	 * @param GmsServerParams $serverParams
	 * @param GmsMessageRequest $request
	 * @return string
	 * @throws GmsSenderError
	 */
	public function send(GmsServerParams $serverParams, GmsMessageRequest $request) : string {

		$request_json = \json_encode($request, JSON_UNESCAPED_UNICODE);
		if($request_json === false){
			throw new GmsSenderError(\json_last_error_msg(), \json_last_error());
		}

		$url = $serverParams->getUrl();

		try{
			$this->logger->debug('REQUEST: '.$request_json);
			$body_steam = ($this->stringStreamFactory)($request_json);
			$http_request = $this->requestFactory
				->createServerRequest('POST', $url)
				->withHeader('Authorization', 'Basic '.\base64_encode($serverParams->getLogin().':'.$serverParams->getPassword()))
				->withHeader('Content-Type', 'application/json; charset=utf-8')
				->withHeader('Content-length', \strlen($request_json))
				->withBody($body_steam);

			$http_response = $this->httpClient->sendRequest($http_request);
			$response_json = $http_response->getBody()->getContents();
			$status_code = $http_response->getStatusCode();
			$this->logger->debug("RESPONSE ($status_code): ".$response_json);
		}catch(ClientExceptionInterface $e){
			$code = $e->getCode();
			$this->logger->warning($e->getMessage());
			throw new GmsSenderError("Request to $url failed ($code): ".$e->getMessage(), 0, $e);
		}

		if($status_code !== 200){
			$this->logger->warning('status: '.$status_code);
			throw new GmsSenderError("Request to $url failed: ".$http_response->getReasonPhrase());
		}

		$data = \json_decode($response_json, true);
		if($data === false){
			$this->logger->warning('Invalid json: '.$response_json);
			throw new GmsSenderError("Invalid response: $response_json");
		}

		if(!\array_key_exists('message_id', $data)){
			$error_code = $data['error_code'] ?? null;
			$error_text = $data['error_text'] ?? 'Unknown Error: '.$response_json;
			$this->logger->warning($error_text);
			throw new GmsSenderError($error_text, $error_code);
		}

		return $data['message_id'];
	}

}
