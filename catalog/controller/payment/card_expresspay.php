<?php
class ControllerPaymentCardExpressPay extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['redirect'] = $this->url->link('payment/card_expresspay/send');
		$data['text_loading'] = $this->language->get('text_loading');
        
		return $this->load->view('default/template/payment/card_expresspay.tpl', $data);
	}
	
	public function send() {
		$this->log_info('send', 'Initialization request for add invoice');
		$this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$secret_word = $this->config->get('card_expresspay_secret_key');
		$is_use_signature = ( $this->config->get('card_expresspay_sign_invoices') == 'on' ) ? true : false;

		$url = ( $this->config->get('card_expresspay_test_mode') != 'on' ) ? $this->config->get('card_expresspay_url_api') : $this->config->get('card_expresspay_url_sandbox_api');
        $url .= "/v1/cardinvoices";
	    $currency = (date('y') > 16 || (date('y') >= 16 && date('n') >= 7)) ? '933' : '974';
        $amount = str_replace('.',',',$this->currency->format($order_info['total'], $this->session->data['currency'], '', false));
        $invoice_info = $this->config->get('card_expresspay_invoice_info');
        $invoice_info = str_replace("##order_id##", $this->session->data['order_id'], $invoice_info);
        
        $request_params = array(
            "Token" => $this->config->get('card_expresspay_token'),
            "AccountNo" => $this->session->data['order_id'],
            "Amount" => $amount,
            "Currency" => $currency,
            "Info" => $invoice_info,
            "ReturnUrl" => $this->url->link('payment/card_expresspay/success'),
            "FailUrl" => $this->url->link('payment/card_expresspay/fail'),
            "SessionTimeoutSecs" => intval($this->config->get('card_expresspay_session_timeout_secs'))
        );

        $add_invoice_url = $url . "?token=" . $this->config->get('card_expresspay_token');
        
        if($is_use_signature)
        	$add_invoice_url .= "&signature=" . $this->compute_signature_add_invoice($request_params, $secret_word);
        
        $request_params_send = array(
            "AccountNo" => $this->session->data['order_id'],
            "Amount" => $amount,
            "Currency" => $currency,
            "Info" => $invoice_info,
            "ReturnUrl" => $this->url->link('payment/card_expresspay/success'),
            "FailUrl" => $this->url->link('payment/card_expresspay/fail'),
            "SessionTimeoutSecs" => intval($this->config->get('card_expresspay_session_timeout_secs'))
        );

        $request_params_send = http_build_query($request_params_send);
        
        $this->log_info('send', 'Send POST request; ORDER ID - ' . $this->session->data['order_id'] . '; URL - ' . $add_invoice_url . '; REQUEST - ' . $request_params_send);

        $response = "";

        try {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $add_invoice_url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params_send);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        $response = curl_exec($ch);
	        curl_close($ch);
    	} catch (Exception $e) {
    		$this->log_error_exception('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response, $e);

    		$this->response->redirect($this->url->link('payment/card_expresspay/fail'));
    	}

    	$this->log_info('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response);

		try {
        	$response = json_decode($response);
    	} catch (Exception $e) {
    		$this->log_error_exception('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response, $e);

    		$this->response->redirect($this->url->link('payment/card_expresspay/fail'));
    	}
        
        if(isset($response->Error['Code']) || !isset($response->CardInvoiceNo)){
            $this->log_error('send', 'Get error; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response . '; ERROR - ������ ����������� �����');

    		$this->response->redirect($this->url->link('payment/card_expresspay/fail'));
        }
        
        $form_url = $url . '/' . $response->CardInvoiceNo . '/payment?token=' . $this->config->get('card_expresspay_token');
        
        if($is_use_signature){
            $requestParams = array(
                 "Token" => $this->config->get('card_expresspay_token'),
                 "CardInvoiceNo" => $response->CardInvoiceNo
             );
            $form_url .= "&signature=" . $this->compute_signature_get_payment_form($requestParams, $secret_word);   
        }
            
        $this->log_info('send', 'Send GET request; ORDER ID - ' . $this->session->data['order_id'] . '; URL - ' . $form_url . '; REQUEST - ' . $response->CardInvoiceNo);

        $response = '';
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $form_url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            $this->log_error_exception('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response, $e);

    		$this->response->redirect($this->url->link('payment/card_expresspay/fail'));
        }
        $this->log_info('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response);
        try {
            $response = json_decode($response);
        } catch (Exception $e) {
            $this->log_error_exception('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response, $e);

    		$this->response->redirect($this->url->link('payment/card_expresspay/fail'));
        }
        
        if(isset($response->Error['Code'])  || !isset($response->FormUrl)){
            $this->log_error('send', 'Get error; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response . '; ERROR - ������ ��������������� �� ����� ������');

    		$this->response->redirect($this->url->link('payment/card_expresspay/fail'));
        }
        $returnUrl = str_replace("https://192.168.10.95","https://192.168.10.95:9090",$response->FormUrl);
        
        $this->response->redirect($returnUrl);
	}

	public function success() {
		$this->log_info('send', 'End request for add invoice');
		$this->log_info('success', 'Initialization render success page; ORDER ID - ' . $this->session->data['order_id']);

		$this->cart->clear();

		$this->load->language('payment/card_expresspay');
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_message'] = $this->language->get('text_message');
		$this->document->setTitle($this->data['heading_title']);

		$data['breadcrumbs'] = array(); 

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/cart'),
			'text'      => $this->language->get('text_basket'),
			'separator' => $this->language->get('text_separator')
		);

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	

		$data['button_continue'] = $this->language->get('button_continue');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['continue'] = $this->url->link('common/home');
		$data['test_mode_label'] = $this->language->get('test_mode_label');
		$data['text_send_notify_success'] = $this->language->get('text_send_notify_success');
		$data['text_send_notify_cancel'] = $this->language->get('text_send_notify_cancel');
		$data['test_mode'] = ( $this->config->get('card_expresspay_test_mode') == 'on' ) ? true : false;
		$data['message_success'] = nl2br($this->config->get('card_expresspay_message_success'), true);
        $data['order_id'] = $this->session->data['order_id'];
		$data['message_success'] = str_replace("##order_id##", $data['order_id'], $data['message_success']);
		$data['is_use_signature'] = ( $this->config->get('card_expresspay_sign_invoices') == 'on' ) ? true : false;
		$data['signature_success'] = $data['signature_cancel'] = "";

		if($data['is_use_signature']) {
			$secret_word = $this->config->get('card_expresspay_secret_key_notify');
			$data['signature_success'] = $this->compute_signature('{"CmdType": 1, "AccountNo": ' . $data["order_id"] . '}', $secret_word);
			$data['signature_cancel'] = $this->compute_signature('{"CmdType": 2, "AccountNo": ' . $data["order_id"] . '}', $secret_word);
		}

		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('card_expresspay_pending_status_id'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->log_info('success', 'End render success page; ORDER ID - ' . $this->session->data['order_id']);

		$this->response->setOutput($this->load->view('payment/card_expresspay_success.tpl', $data));
	}

	public function fail() {
		$this->log_info('send', 'End request for add invoice');
		$this->log_info('fail', 'Initialization render fail page; ORDER ID - ' . $this->session->data['order_id']);

		$this->load->language('payment/card_expresspay');
		$data['heading_title'] = $this->language->get('heading_title_error');
		$data['text_message'] = $this->language->get('text_message_error');
		$this->document->setTitle($this->data['heading_title']);

		$data['breadcrumbs'] = array(); 

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/cart'),
			'text'      => $this->language->get('text_basket'),
			'separator' => $this->language->get('text_separator')
		);

		$data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
        
        $data['message_fail'] = nl2br($this->config->get('card_expresspay_message_fail'), true);
        $data['order_id'] = $this->session->data['order_id'];
		$data['message_fail'] = str_replace("##order_id##", $data['order_id'], $data['message_fail']);
		$data['button_continue'] = $this->language->get('button_continue');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['continue'] = $this->url->link('checkout/checkout');

		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('card_expresspay_cancel_status_id'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->log_info('fail', 'End render fail page; ORDER ID - ' . $this->session->data['order_id']);

		$this->response->setOutput($this->load->view('default/template/payment/card_expresspay_failure.tpl', $data));
	}

    private function compute_signature_add_invoice($request_params, $secret_word) {
    	$secret_word = trim($secret_word);
        $normalized_params = array_change_key_case($request_params, CASE_LOWER);
        $api_method = array(
            "token",
            "accountno",                 
            //"expiration",             
            "amount",                  
            "currency",
            "info",      
            "returnurl",
            "failurl",
            //"language",
            //"pageview",
            "sessiontimeoutsecs"//,
            //"expirationdate"
        );

        $result = "";

        foreach ($api_method as $item)
            $result .= ( isset($normalized_params[$item]) ) ? $normalized_params[$item] : '';

        $hash = strtoupper(hash_hmac('sha1', $result, $secret_word));

        return $hash;
    }
    
    private function compute_signature_get_payment_form($request_params, $secret_word) {
    	$secret_word = trim($secret_word);
        $normalized_params = array_change_key_case($request_params, CASE_LOWER);
        $api_method = array(
            "token",
            "cardinvoiceno"
        );

        $result = "";
        
        foreach ($api_method as $item)
            $result .= ( isset($normalized_params[$item]) ) ? $normalized_params[$item] : '';
        
        $hash = strtoupper(hash_hmac('sha1', $result, $secret_word));
        
        return $hash;
    }

    private function log_error_exception($name, $message, $e) {
    	$this->log($name, "ERROR" , $message . '; EXCEPTION MESSAGE - ' . $e->getMessage() . '; EXCEPTION TRACE - ' . $e->getTraceAsString());
    }

    private function log_error($name, $message) {
    	$this->log($name, "ERROR" , $message);
    }

    private function log_info($name, $message) {
    	$this->log($name, "INFO" , $message);
    }

    private function log($name, $type, $message) {
    	$log = new Log('card_expresspay/express-pay-' . date('Y.m.d') . '.log');
    	$log->write($type . " - IP - " . $_SERVER['REMOTE_ADDR'] . "; USER AGENT - " . $_SERVER['HTTP_USER_AGENT'] . "; FUNCTION - " . $name . "; MESSAGE - " . $message . ';');
    }
}

?>