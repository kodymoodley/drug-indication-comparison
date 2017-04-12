<?php

include('./httpful.phar');

set_time_limit(0);

class HypothesisAPI {
	protected $baseUrl = 'https://hypothes.is/api/';

	/**
	 * Search for annotations.
	 * @param $params array See http://h.readthedocs.org/en/latest/api.html#search
	 * @param $token Optional authorization token
	 * @return array Set of matching annotations
	 */
	public function search($params, $token = null) {
		$response = \Httpful\Request::get($this->baseUrl . 'search?' . http_build_query($params))
			->addHeader('Authorization', $token?"Bearer $token":null)
			->send();

		if ($response->code != '200') throw new Exception('Unexpected service response ' . $response->code);
		return $response->body->rows;
	}

	/**
	 * Read an annotation.
	 * @param $id string ID of annotation to read
	 * @param $token string Optional authorization token
	 * @return Object See http://h.readthedocs.io/en/latest/api.html#read
	 */
	public function read($id, $token = null) {
		$response = \Httpful\Request::get($this->baseUrl . 'annotations/' . urlencode($id))
			->addHeader('Authorization', $token?"Bearer $token":null)
			->send();

		if ($response->code != '200') throw new Exception('Unexpected service response ' . $response->code);
		return $response->body;
	}

	/**
	 * Create an annotation.
	 * @param $annotation array See http://h.readthedocs.io/en/latest/api.html#create
	 * @param $token string Authorization token
	 * @return Object Resultant annotation; see http://h.readthedocs.io/en/latest/api.html#read
	 */
	public function create($annotation, $token) {
		$response = \Httpful\Request::post($this->baseUrl . 'annotations')
			->sendsJson()
			->addHeader('Authorization', "Bearer $token")
			->body(json_encode($annotation))
			->send();

		if ($response->code != '200') throw new Exception('Unexpected service response ' . $response->code);
		return $response->body;
	}

	/**
	 * Delete an annotation by ID.
	 * @param $id string ID of annotation to delete
	 * @param $token string Authorization token
	 */
	public function delete($id, $token) {
		$response = \Httpful\Request::delete($this->baseUrl . 'annotations/' . urlencode($id))
			->addHeader('Authorization', $token?"Bearer $token":null)
			->send();

		if ($response->code != '200') throw new Exception('Unexpected service response ' . $response->code);
		return $response->body;
	}
}

class DailyMedAPI {
	protected $baseUrl = 'https://dailymed.nlm.nih.gov/dailymed/services/v2/';

	/**
	 * Get the SET ID (DailyMed ID) of a drug.
	 * @param $name name of the drug
	 * @return Object See https://dailymed.nlm.nih.gov/dailymed/app-support-web-services.cfm
	 */
	public function getSPLInfo($name) {
		//$response = \Httpful\Request::get($this->baseUrl . 'https://dailymed.nlm.nih.gov/dailymed/services/v2/spls.json?drug_name=' . urlencode($name))
		$response = \Httpful\Request::get($this->baseUrl . 'spls.json?drug_name=' . $name)
			->send();

		if ($response->code == '404' || $response->code == '415' || $response->code == '500') throw new Exception('Unexpected service response ' . $response->code);
		return $response->body;
	}
}

