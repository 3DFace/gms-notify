<?php

namespace dface\GmsNotify;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;

class GmsSender {

	/** @var Client */
	private $httpClient;
	/** @var LoggerInterface */
	private $logger;

	public function __construct(Client $httpClient, LoggerInterface $logger){
		$this->httpClient = $httpClient;
		$this->logger = $logger;
	}

	function send(GmsServerParams $serverParams, GmsMessageRequest $request) : string {

		$request_json = json_encode($request, JSON_UNESCAPED_UNICODE);
		if($request_json === false){
			throw new GmsSenderError(json_last_error_msg(), json_last_error());
		}

		$options = [
			'headers' => [
				'Authorization' => 'Basic '.base64_encode($serverParams->getLogin().':'.$serverParams->getPassword()),
				'Content-Type' => 'application/json; charset=utf-8',
				'Content-length' => strlen($request_json),
			],
			'body' => $request_json,
		];

		$url = $serverParams->getUrl();

		try{
			$this->logger->debug('REQUEST: '.$request_json);
			$g_request = $this->httpClient->request('POST', $url, $options);
			$response_json = $g_request->getBody()->getContents();
			$status_code = $g_request->getStatusCode();
			$this->logger->debug("RESPONSE ($status_code): ".$response_json);
		}catch(ClientException $e){
			$code = $e->getCode();
			$this->logger->warning($e->getMessage());
			throw new GmsSenderError("Request to $url failed ($code): ".$e->getMessage(), 0, $e);
		}

		if((int)$status_code !== 200){
			$this->logger->warning('status: '.$status_code);
			throw new GmsSenderError("Request to $url failed: ".$g_request->getReasonPhrase());
		}

		$data = json_decode($response_json, true);
		if($data === false){
			$this->logger->warning('Invalid json: '.$response_json);
			throw new GmsSenderError("Invalid response: $response_json");
		}

		if(!array_key_exists('message_id', $data)){
			$error_code = $data['error_code'] ?? null;
			$error_text = $data['error_text'] ?? 'Unknown Error: '.$response_json;
			$this->logger->warning($error_text);
			throw new GmsSenderError($error_text, $error_code);
		}

		return $data['message_id'];
	}

}
