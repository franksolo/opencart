<?php
class ModelSaleOrder extends Model {
	public function addOrder($data) {
		if ($data['order_status_id']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($this->config->get('config_invoice_prefix')) . "'");
	
			if ($query->row['invoice_no']) {
				$invoice_no = (int)$query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}
		} else {
			$invoice_no = 0;
		}
		exit($invoice_no);
		$this->load->model('setting/store');
		
		$store_info = $this->model_setting_store->getStore($data['store_id']);
		
		if ($store_info) {
			$store_name = $store_info['name'];
			$store_url = $store_info['url'];
		} else {
			$store_name = $this->config->get('config_name');
			$store_url = HTTP_CATALOG;			
		}
		
		$this->load->model('sale/customer');
		
		$customer_info = $this->model_sale_customer->getCustomer($data['customer_id']);
		
		if ($customer_info) {
			$customer_group_id = $customer_info['customer_group_id'];
		} elseif ($store_info) {
			$customer_group_id = $store_info['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');		
		}
				
		$this->load->model('localisation/country');
		
		$country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);
		
		if ($country_info) {
			$shipping_country = $country_info['name'];
			$shipping_address_format = $country_info['address_format'];
		} else {
			$shipping_country = '';	
			$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}	
		
		$this->load->model('localisation/zone');
		
		$zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);
		
		if ($zone_info) {
			$shipping_zone = $zone_info['name'];
		} else {
			$shipping_zone = '';			
		}	
					
		$country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);
		
		if ($country_info) {
			$payment_country = $country_info['name'];
			$payment_address_format = $country_info['address_format'];			
		} else {
			$payment_country = '';	
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
		}
	
		$zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);
		
		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';			
		}	

		$this->load->model('localisation/currency');

		$currency_info = $this->getCurrencyByCode($this->config->get('config_currency'));
		
		if ($currency_info) {
			$currency_id = $currency_info['currency_id'];
			$currency_value = $currency_info['value'];
		} else {
			$currency_id = 0;
			$currency_value = 1.00000;			
		}
		
      	$this->db->query("INSERT INTO " . DB_PREFIX . "order SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($this->config->get('config_invoice_prefix')) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($store_name) . "', store_url = '" . $this->db->escape($store_url) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$customer_group_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . ", shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$total . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', language_id = '" . (int)$data['affiliate_id'] . "', currency_id = '" . $this->db->escape($this->config->get('config_currency')) . "', currency_code = '" . $this->db->escape($currency_code) . "', currency_value = '" . (float)$currency_value . "', date_added = NOW(), date_modified = NOW()");
      	
      	$order_id = $this->db->getLastId();
      	
      	if (isset($data['order_product'])) {		
      		foreach ($data['order_product'] as $order_product) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$return_product['product_id'] . "', name = '" . $this->db->escape($return_product['name']) . "', model = '" . $this->db->escape($return_product['model']) . "', quantity = '" . (int)$return_product['quantity'] . "', manufacturer = '" . (int)$return_product['manufacturer'] . "', return_reason_id = '" . (int)$return_product['return_reason_id'] . "', opened = '" . (int)$return_product['opened'] . "', comment = '" . $this->db->escape($return_product['comment']) . "', return_action_id = '" . (int)$return_product['return_action_id'] . "'");
			}
			
			$order_product_id = $this->db->getLastId();

			foreach ($product['option'] as $option) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
			}
				
			
		}
		
		foreach ($product['download'] as $download) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int)($download['remaining'] * $product['quantity']) . "'");
		}		
			
      	if (isset($data['order_total'])) {		
      		foreach ($data['order_total'] as $order_total) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$return_product['product_id'] . "', name = '" . $this->db->escape($return_product['name']) . "', model = '" . $this->db->escape($return_product['model']) . "', quantity = '" . (int)$return_product['quantity'] . "', manufacturer = '" . (int)$return_product['manufacturer'] . "', return_reason_id = '" . (int)$return_product['return_reason_id'] . "', opened = '" . (int)$return_product['opened'] . "', comment = '" . $this->db->escape($return_product['comment']) . "', return_action_id = '" . (int)$return_product['return_action_id'] . "'");
			}
		}		
	}
	
	public function editOrder($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return`SET order_id = '" . (int)$data['order_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', invoice_no = '" . $this->db->escape($data['invoice_no']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "return_product WHERE return_id = '" . (int)$return_id . "'");
      	
		if (isset($data['return_product'])) {		
      		foreach ($data['return_product'] as $return_product) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "return_product SET return_id = '" . (int)$return_id . "', product_id = '" . (int)$return_product['product_id'] . "', name = '" . $this->db->escape($return_product['name']) . "', model = '" . $this->db->escape($return_product['model']) . "', quantity = '" . (int)$return_product['quantity'] . "', return_reason_id = '" . (int)$return_product['return_reason_id'] . "', opened = '" . (int)$return_product['opened'] . "', comment = '" . $this->db->escape($return_product['comment']) . "', return_action_id = '" . (int)$return_product['return_action_id'] . "'");
			}
		} 
	}
		
	public function deleteOrder($order_id) {
		if ($this->config->get('config_stock_subtract')) {
			$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

			if ($order_query->num_rows) {
				$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

				foreach($product_query->rows as $product) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "'");

					$option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

					foreach ($option_query->rows as $option) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
	  	$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_history WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer, (SELECT CONCAT(a.firstname, ' ', a.lastname) FROM " . DB_PREFIX . "affiliate a WHERE a.affiliate_id = o.affiliate_id) AS affiliate FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}
			
			$order_data = $order_query->row;
			$order_data['shipping_zone_code']  = $shipping_zone_code;
			$order_data['shipping_iso_code_2'] = $shipping_iso_code_2;
			$order_data['shipping_iso_code_3'] = $shipping_iso_code_3;
			$order_data['payment_zone_code']   = $payment_zone_code;
			$order_data['payment_iso_code_2']  = $payment_iso_code_2;
			$order_data['payment_iso_code_3' ] = $payment_iso_code_3;

			return $order_data;
		} else {
			return FALSE;
		}
	}

	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (isset($data['filter_order_id']) && !is_null($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (isset($data['filter_customer']) && !is_null($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_total']) && !is_null($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'name',
			'status',
			'o.date_added',
			'o.date_modified',
			'o.total',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}
	
	public function getOrderOption($order_id, $order_option_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_option_id = '" . (int)$order_option_id . "'");

		return $query->row;
	}
	
	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
	
	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderDownloads($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' ORDER BY name");

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";

		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

		if (isset($data['filter_order_id']) && !is_null($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (isset($data['filter_customer']) && !is_null($data['filter_customer'])) {
			$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_total']) && !is_null($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByStoreId($store_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByLanguageId($language_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByCurrencyId($currency_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalSales() {
      	$query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalSalesByYear($year) {
      	$query = $this->db->query("SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND YEAR(date_added) = '" . (int)$year . "'");

		return $query->row['total'];
	}

	public function addOrderHistory($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		if ($data['append']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
		}

      	if ($data['notify']) {
        	$order_query = $this->db->query("SELECT *, os.name AS status FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id AND os.language_id = o.language_id) LEFT JOIN " . DB_PREFIX . "language l ON (o.language_id = l.language_id) WHERE o.order_id = '" . (int)$order_id . "'");

			if ($order_query->num_rows) {
				$language = new Language($order_query->row['directory']);
				$language->load($order_query->row['filename']);
				$language->load('sale/order');

				$subject = sprintf($language->get('mail_subject'), $order_query->row['store_name'], $order_id);

				$message  = $language->get('mail_order') . ' ' . $order_id . "\n";
				$message .= $language->get('mail_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_query->row['date_added'])) . "\n\n";
				$message .= $language->get('mail_order_status') . "\n\n";
				$message .= $order_query->row['status'] . "\n\n";
				$message .= $language->get('mail_invoice') . "\n";
				$message .= html_entity_decode($order_query->row['store_url'] . 'index.php?route=account/invoice&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";

				if ($data['comment']) {
					$message .= $language->get('mail_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}

				$message .= $language->get('mail_footer');

				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');
				$mail->setTo($order_query->row['email']);
				$mail->setFrom($this->config->get('config_email'));
	    		$mail->setSender($order_query->row['store_name']);
	    		$mail->setSubject($subject);
	    		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
	    		$mail->send();
			}
		}
	}
		
	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	
	public function getTotalOrderHistories($order_id) {
	  	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}	
		
	public function getOrderHistoriesTotalByOrderStatusId($order_status_id) {
	  	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

		return $query->row['total'];
	}	
}
?>