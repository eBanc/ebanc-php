<?php

/**
 * API Main.
 * @author Kevin Kaske
 */
class Ebanc {
	
	protected $apiKey;
	protected $apiVersion;
	protected $gatewayId;
	protected $server;
	protected $ebancUrl;
	
	/**
	 * Constructor.
	 *
	 * @param string $apiKey
	 * @param string $gatewayId
	 * @param integer $apiVersion
	 * @author Kevin Kaske
	 */
	public function __construct($apiKey, $gatewayId) {
		$this->apiKey = $apiKey;
		$this->gatewayId = $gatewayId;
		$this->apiVersion = 2;
		
		$this->server = 'https://'.$gatewayId.'.ebanccorp.com';
		$this->ebancUrl = $this->server.'/api/v'.$apiVersion;
	}
	
	/* -------------------------------
			  Custom Settings
	------------------------------- */
	
	/**
	 * Set server address. Used for testing.
	 *
	 * @param string $server
	 * @author Kevin Kaske
	 */
	public function setServer($server){
		$this->server = $server;
		$this->ebancUrl = $this->server.'/api/v'.$this->apiVersion;
	}
	
	/**
	 * Set API Version.
	 *
	 * @param integer $apiVersion
	 * @author Kevin Kaske
	 */
	public function setAPIVersion($apiVersion){
		$this->apiVersion = $apiVersion;
		$this->ebancUrl = $this->server.'/api/v'.$this->apiVersion;
	}
	
	/* -------------------------------
			  Customers
	------------------------------- */
	
	/**
	 * Gets all customers.
	 *
	 * @return array of Customer Objects by Hash
	 * @author Kevin Kaske
	 */
	public function getCustomers() {
		$url = $this->ebancUrl.'/customers';
		return sendData($url);
	}
	
	/**
	 * Gets a specific customer.
	 *
	 * @param string $uuid
	 * @return Customer Object
	 * @author Kevin Kaske
	 */
	public function getCustomer($uuid) {
		$url = $this->ebancUrl.'/customers/'.$uuid;
		return sendData($url);
	}
	
	/**
	 * Create customer.
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $routingNumber
	 * @param string $accountNumber
	 * @return Customer Objects by Hash
	 * @author Kevin Kaske
	 */
	public function createCustomer($firstName, $lastName, $routingNumber, $accountNumber) {
		$url = $this->ebancUrl.'/customers';
		$fields = array('first_name' => $firstName, 'last_name' => $lastName, 'account_number' => $accountNumber, 'routing_number' => $routingNumber);
		return sendData($url, true, $fields);
	}
	
	/**
	 * Update customer.
	 *
	 * @param string $uuid
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $routingNumber
	 * @param string $accountNumber
	 * @return Customer Objects by Hash
	 * @author Kevin Kaske
	 */
	public function updateCustomer($uuid, $firstName, $lastName, $routingNumber, $accountNumber) {
		$url = $this->ebancUrl.'/customers/'.$uuid;
		$fields = array('first_name' => $firstName, 'last_name' => $lastName, 'account_number' => $accountNumber, 'routing_number' => $routingNumber);
		return sendData($url, true, $fields);
	}
	
	/* -------------------------------
			  Transactions
	------------------------------- */
	/**
	 * Gets last 50 transactions
	 *
	 * @return array of Transaction Objects by Hash
	 * @author Kevin Kaske
	 */
	public function getTransactions() {
		$url = $this->ebancUrl.'/transactions';
		return sendData($url);
	}
	
	/**
	 * Gets a specific transaction.
	 *
	 * @param string $uuid
	 * @return Transaction Object
	 * @author Kevin Kaske
	 */
	public function getTransaction($uuid) {
		$url = $this->ebancUrl.'/transactions/'.$uuid;
		return sendData($url);
	}
	
	/**
	 * Create transaction.
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $routingNumber
	 * @param string $accountNumber
	 * @return Transactions Object
	 * @author Kevin Kaske
	 */
	public function createTransaction($firstName, $lastName, $routingNumber, $accountNumber, $amount, $category = null, $memo = null) {
		$url = $this->ebancUrl.'/transactions';
		$fields = array('first_name' => $firstName, 'last_name' => $lastName, 'account_number' => $accountNumber, 'routing_number' => $routingNumber, 'amount' => $amount, 'category' => $category, 'memo' => $memo);
		return sendData($url, true, $fields);
	}
	
	/**
	 * Create transaction.
	 *
	 * @param string $customerUUID
	 * @return Transactions Object
	 * @author Kevin Kaske
	 */
	public function createTransactionForCustomer($customerUUID, $amount, $category = null, $memo = null) {
		$url = $this->ebancUrl.'/transactions';
		$fields = array('customer_uuid' => $customerUUID, 'amount' => $amount, 'category' => $category, 'memo' => $memo);
		return sendData($url, true, $fields);
	}
	
	/* -------------------------------
			  Utility Functions
	------------------------------- */
	
	function sendData($url, $post = false, $fields = null){
		$headers = array (
		    "Authentication: Token token=\"".$this->apiKey."\""
		);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST,$post);
		if($fields != null){
			curl_setopt($curl, CURLOPT_POSTFIELDS,$fields);
		}
		return curl_exec($curl);
	}
}

?>