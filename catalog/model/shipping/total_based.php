<?php
class ModelShippingTotalBased extends Model {
  function getQuote($address) {
    $this->language->load('shipping/total_based');
		
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('total_based_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
    if (!$this->config->get('total_based_geo_zone_id')) {
      $status = true;
    } elseif ($query->num_rows) {
      $status = true;
    } else {
      $status = false;
    }
		
    $method_data = array();
	
    if ($status) {
      $total = $this->cart->getSubTotal();
      $cost = $total > $this->config->get('total_based_threshold') ? 0 : $this->config->get('total_based_cost');
      
      $quote_data = array();
			
      $quote_data['total_based'] = array(
	'code'         => 'total_based.total_based',
	'title'        => sprintf($this->language->get('text_description'), $this->config->get('total_based_threshold')),
	'cost'         => $cost,
	'tax_class_id' => $this->config->get('total_based_tax_class_id'),
	'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('total_based_tax_class_id'), $this->config->get('config_tax')))
      );

      $method_data = array(
	'code'       => 'total_based',
	'title'      => $this->language->get('text_title'),
	'quote'      => $quote_data,
	'sort_order' => $this->config->get('total_based_sort_order'),
	'error'      => false
      );
    }
	
    return $method_data;
  }
}
?>