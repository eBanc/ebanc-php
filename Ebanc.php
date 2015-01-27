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
	protected $errorMessage;
	
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
		
		$this->server = 'https://'.$this->gatewayId.'.ebanccorp.com';
		$this->ebancUrl = $this->server.'/api/v'.$this->apiVersion;
	}
	
	/* -------------------------------
				Custom Settings (Not common to set)
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
	 * Gets customers for this account.
	 * Suppoerts search on customer name
	 * and pagination
	 *
	 * @param string query
	 * @param integer page
	 * @param integer per_page
	 * @return array of Customer Objects by Hash
	 * @author Kevin Kaske
	 */
	public function getCustomers($query=null, $page=null, $perPage=30) {
		$url = $this->ebancUrl.'/customers';
		if($page){
			$url = $url.'?page='.$page.'&per_page='.$perPage;
		}
		
		if($query){
			//Add or Append to url params
			if(strpos($url,'?') !== false){
				$url = $url.'&';
			}else{
				$url = $url.'?';
			}
			
			$url = $url.'query='.$query;
		}
		$customers = $this->queryApi($url);
		
		if(count($customers['customers']) == 0){
			$this->errorMessage = 'No customers found';
		}
		
		return $customers['customers'];
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
		$customer = $this->queryApi($url);
		
		if(count($customer) == 0){
			$this->errorMessage = 'Customer not found';
			return false;
		}else{
			return $customer;
		}
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
		$customer = $this->queryApi($url, true, false, $fields);
		
		if(isset($customer['base'])){
			$this->errorMessage = $customer['base'][0];
			return false;
		}else{
			return $customer;
		}
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
	public function updateCustomer($uuid, $firstName, $lastName, $routingNumber = null, $accountNumber = null) {
		$url = $this->ebancUrl.'/customers/'.$uuid;
		$fields = array('first_name' => $firstName, 'last_name' => $lastName);
		if($accountNumber){
			$fields['account_number'] = $accountNumber;
		}
		if($routingNumber){
			$fields['routing_number'] = $routingNumber;
		}
		
		if($this->getCustomer($uuid)){
			$customer = $this->queryApi($url, true, true, $fields);
			if(isset($customer['base'])){
				$this->errorMessage = $customer['base'][0];
				return false;
			}else{
				return $customer;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Delete customer.
	 *
	 * @param string $uuid
	 * @return Void
	 * @author Kevin Kaske
	 */
	public function deleteCustomer($uuid){
		$url = $this->ebancUrl.'/customers/'.$uuid;
		$this->queryApi($url, $post = false, $patch = false, $fields = null, true);
	}
	
	/* -------------------------------
				Recurring
	------------------------------- */
	
	/**
	 * Gets recurring for this account.
	 * Suppoerts search on recurring name
	 * and pagination
	 *
	 * @param string recurringType
	 * @param integer page
	 * @param integer perPage
	 * @return array of Recurring Objects by Hash
	 * @author Kevin Kaske
	 */
	public function getRecurrings($recurringType=null, $page=null, $perPage=30) {
		$url = $this->ebancUrl.'/recurrings';
		if($page){
			$url = $url.'?page='.$page.'&per_page='.$perPage;
		}
		
		if($recurringType){
			//Add or Append to url params
			if(strpos($url,'?') !== false){
				$url = $url.'&';
			}else{
				$url = $url.'?';
			}
			
			$url = $url.'recurring_type='.$recurringType;
		}
		$recurring = $this->queryApi($url);
		
		if(count($recurring['recurring']) == 0){
			$this->errorMessage = 'No recurring found';
		}
		
		return $recurring['recurring'];
	}
	
	/**
	 * Gets a specific recurring.
	 *
	 * @param string $uuid
	 * @return Recurring Object
	 * @author Kevin Kaske
	 */
	public function getRecurring($uuid) {
		$url = $this->ebancUrl.'/recurrings/'.$uuid;
		$recurring = $this->queryApi($url);
		
		if(count($recurring) == 0){
			$this->errorMessage = 'Recurring not found';
			return false;
		}else{
			return $recurring;
		}
	}
	
	/**
	 * Is Even Week
	 *
	 * @return Boolean
	 * @author Kevin Kaske
	 */
	public function isEvenWeek() {
		$url = $this->ebancUrl.'/recurrings/is_even_week';
		if($this->queryApi($url) == 'true'){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Create recurring.
	 *
	 * @param string $customer_uuid
	 * @param string $recurring_type
	 * @param string $recurring_options
	 * @param string $amount
	 * @param string $category
	 * @return Recurring Objects by Hash
	 * @author Kevin Kaske
	 */
	public function createRecurring($customer_uuid, $transaction_type, $recurring_type, $recurring_options, $amount, $category=null) {
		$url = $this->ebancUrl.'/recurrings';
		$fields = array('transaction_type' => $transaction_type, 'customer_uuid' => $customer_uuid, 'recurring_type' => $recurring_type, 'recurring_options' => $recurring_options, 'amount' => $amount);
		if($category){
			$fields['category'] = $category;
		}
		$recurring = $this->queryApi($url, true, false, $fields);
		
		if(isset($recurring['base'])){
			$this->errorMessage = $recurring['base'][0];
			return false;
		}else{
			return $recurring;
		}
	}
	
	/**
	 * Update recurring.
	 *
	 * @param string $uuid
	 * @param string $customer_uuid
	 * @param string $recurring_type
	 * @param string $recurring_options
	 * @param string $amount
	 * @param string $category
	 * @return Recurring Objects by Hash
	 * @author Kevin Kaske
	 */
	public function updateRecurring($uuid, $customer_uuid, $recurring_type, $recurring_options, $amount, $category=null) {
		$url = $this->ebancUrl.'/recurrings/'.$uuid;
		$fields = array('customer_uuid' => $customer_uuid, 'recurring_type' => $recurring_type, 'recurring_type' => $recurring_type, 'recurring_options' => $recurring_options, 'amount' => $amount);
		if($category){
			$fields['category'] = $category;
		}
		
		if($this->getRecurring($uuid)){
			$recurring = $this->queryApi($url, true, true, $fields);
			if(isset($recurring['base'])){
				$this->errorMessage = $recurring['base'][0];
				return false;
			}else{
				return $recurring;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Delete recurring.
	 *
	 * @param string $uuid
	 * @return Void
	 * @author Kevin Kaske
	 */
	public function deleteRecurring($uuid){
		$url = $this->ebancUrl.'/recurrings/'.$uuid;
		$this->queryApi($url, $post = false, $patch = false, $fields = null, true);
	}
	
	/* -------------------------------
				Transactions
	------------------------------- */
	/**
	 * Gets transactions for this account
	 * defaults to the latest 50 transaction
	 * supports pagination
	 *
	 * @param int $page
	 * @param int $perPage
	 * @return array of Transaction Objects by Hash
	 * @author Kevin Kaske
	 */
	public function getTransactions($page=null, $perPage=50) {
		$url = $this->ebancUrl.'/transactions';
		if($page){
			$url = $url.'?page='.$page.'&per_page='.$perPage;
		}
		$transactions = $this->queryApi($url);
		
		if(count($transactions['transactions']) == 0){
			$this->errorMessage = 'No transactions found';
		}
		
		return $transactions['transactions'];
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
		$transaction = $this->queryApi($url);
		
		if(count($transaction) == 0){
			$this->errorMessage = 'Transaction not found';
			return false;
		}else{
			return $transaction;
		}
	}
	
	/**
	 * Gets categories for this account that transactions have
	 * been submitted to
	 *
	 * @return array of Category Objects by Hash
	 * @author Kevin Kaske
	 */
	public function getCategories() {
		$url = $this->ebancUrl.'/transactions/categories';
		$categories = $this->queryApi($url);
		
		if(count($categories['categories']) == 0){
			$this->errorMessage = 'No categories found';
		}
		
		return $categories['categories'];
	}
	
	/**
	 * Create transaction.
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $routingNumber
	 * @param string $accountNumber
	 * @param float $amount
	 * @param string $type
	 * @param string $category
	 * @param string $memo
	 * @return Transactions Object
	 * @author Kevin Kaske
	 */
	public function createTransaction($firstName, $lastName, $routingNumber, $accountNumber, $amount, $type = 'debit', $category = null, $memo = null) {
		$url = $this->ebancUrl.'/transactions';
		$fields = array('first_name' => $firstName, 'last_name' => $lastName, 'account_number' => $accountNumber, 'routing_number' => $routingNumber, 'amount' => $amount, 'category' => $category, 'memo' => $memo, 'transaction_type' => $type);
		$transaction = $this->queryApi($url, true, false, $fields);
		
		if(isset($transaction['base'])){
			$this->errorMessage = $transaction['base'][0];
			return false;
		}else{
			return $transaction;
		}
	}
	
	/**
	 * Create transaction for customer
	 *
	 * @param string $customerUUID
	 * @param float $amount
	 * @param string $type
	 * @param string $category
	 * @param string $memo
	 * @return Transactions Object
	 * @author Kevin Kaske
	 */
	public function createTransactionForCustomer($customerUUID, $amount, $type = 'debit', $category = null, $memo = null) {
		$url = $this->ebancUrl.'/transactions';
		$fields = array('customer_uuid' => $customerUUID, 'amount' => $amount, 'category' => $category, 'memo' => $memo, 'transaction_type' => $type);
		$transaction = $this->queryApi($url, true, false, $fields);
		
		if(isset($transaction['base'])){
			$this->errorMessage = $transaction['base'][0];
			return false;
		}else{
			return $transaction;
		}
	}
	
	/* -------------------------------
				Utility Functions
	------------------------------- */
	
	public function queryApi($url, $post = false, $patch = false, $fields = null, $delete = null){
		$this->errorMessage = '';
		
		$curl = curl_init();
		$token = 'token='.$this->apiKey;
		
		//Add or Append to url params
		if(strpos($url,'?') !== false){
			$token = '&'.$token;
		}else{
			$token = '?'.$token;
		}
		curl_setopt($curl, CURLOPT_URL, $url.'?token='.$this->apiKey);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST,$post);
		
		if($patch){
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		}
		
		if($delete){
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		
		if($fields != null){
			$encoded = '';
			foreach($fields as $name => $value){
				$encoded .= urlencode($name).'='.urlencode($value).'&';
			}
			// chop off the last ampersand
			$encoded = substr($encoded, 0, strlen($encoded)-1);
			curl_setopt($curl, CURLOPT_POSTFIELDS,$encoded);
		}
		
		$result = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if($http_status == 401){
			// Get error msg
			throw new Exception('eBanc API access denied');
		}else{
			return json_decode($result, true);
		}
		
		
	}
	
	public function getError() {
		$error = $this->errorMessage;
		$this->errorMessage = '';
		return $error;
	}
	
	public function isError() {
		if($this->errorMessage == ''){
			return false;
		}else{
			return true;
		}
	}
}
?>