<?php
class ModelPaymentCardExpressPay extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/card_expresspay');
		
		$status = true;
				
		$method_data = array();
		
		if ($status) {
			$method_data = array(
				'code'       => 'card_expresspay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('card_expresspay_sort_order')
			);
		}
		
		return $method_data;
	}
}
?>