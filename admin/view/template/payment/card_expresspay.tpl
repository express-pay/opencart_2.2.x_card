<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">

      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
          <div class="form-group">
            <div class="col-sm-2" style="margin-left: -16px; margin-right: 16px;">
                 <a target="_blank" href="https://express-pay.by"><img src="/admin/view/image/payment/erip_expresspay_big.png" width="270" height="91" alt="exspress-pay.by" title="express-pay.by"></a>
            </div>
            <div class="col-sm-10" style="margin-top: 11px;">
              <?php echo $text_about; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_token"><span data-toggle="tooltip" title="" data-original-title="<?php echo $token_comment; ?>"><?php echo $token_label; ?></span></label>
            <div class="col-sm-10">
              <input required type="text" name="card_expresspay_token" id="card_token" value="<?php echo $card_expresspay_token; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_handler_url"><?php echo $handler_label; ?></label>
            <div class="col-sm-10">
              <input readonly="readonly" type="text" name="card_handler_url" id="card_handler_url" value="<?php echo $handler_url; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="erip_sign_invoices"><span data-toggle="tooltip" title="" data-original-title="<?php echo $sign_comment; ?>"><?php echo $sign_invoices_label; ?></span></label>
            <div class="col-sm-10">
              <input <?php echo ( $card_expresspay_sign_invoices_value == 'on') ? 'checked' : ''; ?> id="card_sign_invoices" style="margin-top: 10px;" type="checkbox" name="card_expresspay_sign_invoices" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_secret_key"><span data-toggle="tooltip" title="" data-original-title="<?php echo $secret_key_comment; ?>"><?php echo $secret_key_label; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_secret_key" id="card_secret_key" value="<?php echo $card_expresspay_secret_key_value; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_sign_notify"><span data-toggle="tooltip" title="" data-original-title="<?php echo $sign_comment; ?>"><?php echo $sign_notify_label; ?></span></label>
            <div class="col-sm-10">
              <input <?php echo ( $card_expresspay_sign_notify_value == 'on') ? 'checked' : ''; ?> id="card_sign_notify" style="margin-top: 10px;" type="checkbox" name="card_expresspay_sign_notify" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_secret_key_notify"><span data-toggle="tooltip" title="" data-original-title="<?php echo $secret_key_notify_comment; ?>"><?php echo $secret_key_notify_label; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_secret_key_notify" id="card_secret_key_notify" value="<?php echo $card_expresspay_secret_key_notify_value; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_invoice_info"><?php echo $invoice_info_label; ?></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_invoice_info" id="card_invoice_info" value="<?php echo ( !empty($card_expresspay_invoice_info) ) ? $card_expresspay_invoice_info_value : 'Оплата заказа №##order_id##'; ?>" class="form-control" />
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_session_timeout_secs"><span data-toggle="tooltip" title="" data-original-title="<?php echo $session_timeout_secs_comment; ?>"><?php echo $session_timeout_secs_label; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_session_timeout_secs" id="card_session_timeout_secs" value="<?php echo $card_expresspay_session_timeout_secs_value; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_test_mode"><?php echo $test_mode_label; ?></label>
            <div class="col-sm-10">
              <input <?php echo ( $card_expresspay_test_mode_value == 'on') ? 'checked' : ''; ?> id="card_test_mode" style="margin-top: 10px;" type="checkbox" name="card_expresspay_test_mode" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_url_api"><?php echo $url_api_label; ?></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_url_api" id="card_url_api" value="<?php echo ( !empty($card_expresspay_url_api_value) ) ? $card_expresspay_url_api_value : 'https://api.express-pay.by'; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_url_sandbox_api"><?php echo $url_sandbox_api_label; ?></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_url_sandbox_api" id="card_url_sandbox_api" value="<?php echo ( !empty($card_expresspay_url_sandbox_api_value) ) ? $card_expresspay_url_sandbox_api_value : 'https://sandbox-api.express-pay.by'; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_expresspay_message_success"><?php echo $message_success_label; ?></label>
            <div class="col-sm-10">
              <textarea class="form-control" style="height: 120px; max-width: 100%;"  id="card_expresspay_message_success" name="card_expresspay_message_success"><?php echo $card_expresspay_message_success_value; ?></textarea>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_expresspay_message_fail"><?php echo $message_fail_label; ?></label>
            <div class="col-sm-10">
              <textarea class="form-control" style="height: 120px; max-width: 100%;"  id="card_expresspay_message_fail" name="card_expresspay_message_fail"><?php echo $card_expresspay_message_fail_value; ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label style="font-size: 20px;" class="col-sm-2 control-label"><?php echo $settings_module_label; ?></label>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_expresspay_status"><?php echo $status_label; ?></label>
            <div class="col-sm-10">
              <select name="card_expresspay_status" id="card_expresspay_status" class="form-control">
                <?php if ($card_expresspay_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_pending_status_id"><?php echo $pending_status; ?></label>
            <div class="col-sm-10">
              <select name="card_expresspay_pending_status_id" id="card_pending_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $card_expresspay_pending_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_processing_status_id"><?php echo $processing_status; ?></label>
            <div class="col-sm-10">
              <select name="card_expresspay_processing_status_id" id="card_processing_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $card_expresspay_processing_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="card_cancel_status_id"><?php echo $cancel_status; ?></label>
            <div class="col-sm-10">
              <select name="card_expresspay_cancel_status_id" id="card_cancel_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $card_expresspay_cancel_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="erip_sort_order"><?php echo $sort_order_label; ?></label>
            <div class="col-sm-10">
              <input type="text" name="card_expresspay_sort_order" id="card_sort_order" value="<?php echo $card_expresspay_sort_order; ?>" size="1" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <div class="copyright" style="text-align: center;">
              &copy; Все права защищены | ООО «ТриИнком», 2013-<?php echo date('Y'); ?><br/>
              <?php echo $text_version . CARD_EXPRESSPAY_VERSION ?>
            </div>
          </div>
      </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 