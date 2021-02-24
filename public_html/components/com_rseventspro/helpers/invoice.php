<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSEventsProInvoice {

    protected $cart = false;
    protected $order_id;
    protected $document;

    public function __construct($order_id = null) {
		if (empty($order_id)) {
            throw new Exception(JText::_('COM_RSEVENTSPRO_ERROR_INVOICE_NO_ORDER_ID'), 500);
        }

        $this->order_id = $order_id;
		
		// Check if the order id is valid and the payment is complete
		if (!$this->isValidOrderId()) {
			throw new Exception(JText::_('COM_RSEVENTSPRO_ERROR_INVOICE_INVALID_OR_NOT_PAYED_ORDER_ID'), 500);
		}
		
		// Check for PDF plugin
		if (!rseventsproHelper::pdf('1.18')) {
			throw new Exception(JText::_('COM_RSEVENTSPRO_ERROR_INVOICE_PDF_PLUGIN'), 500);
		}
		
		JFactory::getApplication()->triggerEvent('onrsepro_isCart',array(array('cart'=>&$this->cart)));
    }

    public static function getInstance($order_id = null) {
        static $instances = array();

        if (!isset($instances[$order_id])) {
            $instances[$order_id] = new RSEventsProInvoice($order_id);
        }

        return $instances[$order_id];
    }
	
	public function output($downloadable = true) {
		$invoice = $this->getTemplate();
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/pdf.php';
		
		$options = array('font' => $this->document->orientation, 'orientation' => $this->document->orientation);
		$pdf	 = RSEventsProPDF::getInstance($options);
		
		if ($downloadable) {
			$pdf->output($invoice, $this->document->title.'.pdf');
			die;
		} else {
			return array('buffer' => $pdf->write($invoice), 'title' => $this->document->title.'.pdf');
		}
	}
	
	protected function isValidOrderId() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('state'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '.$db->q($this->order_id));
		$db->setQuery($query);
		return $db->loadResult() == 1;
	}
	
	protected function getData() {
		static $data = array();
		
		if (!isset($data[$this->order_id])) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear()
				->select('*')
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('id').' = '.$db->q($this->order_id));
			$db->setQuery($query);
			$data[$this->order_id] = $db->loadObject();
		}
		
		return $data[$this->order_id];
	}
	
	protected function getEventInvoiceDetails($id) {
		static $eventInvoice = array();
		
		if (!isset($eventInvoice[$id])) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('invoice'))->select($db->qn('invoice_attach'))->select($db->qn('invoice_type'))->select($db->qn('invoice_font'))
				->select($db->qn('invoice_orientation'))->select($db->qn('invoice_padding'))->select($db->qn('invoice_prefix'))->select($db->qn('invoice_title'))
				->select($db->qn('invoice_layout'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$eventInvoice[$id] = $db->loadObject();
		}
		
		return $eventInvoice[$id];
	}
	
	protected function getGlobalInvoiceDetails() {
		$config = rseventsproHelper::getConfig();
		
		return (object) array('invoice_font' => $config->invoice_font, 'invoice_orientation' => $config->invoice_orientation, 'invoice_padding' => $config->invoice_padding, 'invoice_prefix' => $config->invoice_prefix, 'invoice_title' => $config->invoice_title, 'invoice_layout' => $config->invoice_layout);
	}
	
	protected function getInvoiceDetails($id) {
		if (empty($id)) {
			$events = $this->getCartEvents();
			
			if (count($events) == 1) {
				if (isset($events[0])) {
					$object = $this->getEventInvoiceDetails($events[0])->invoice_type == 1 ? $this->getGlobalInvoiceDetails() : $this->getEventInvoiceDetails($events[0]);
				} else {
					$object = $this->getGlobalInvoiceDetails();
				}
				
			} else {
				$object = $this->getGlobalInvoiceDetails();
			}
		} else {
			$object = $this->getEventInvoiceDetails($id)->invoice_type == 1 ? $this->getGlobalInvoiceDetails() : $this->getEventInvoiceDetails($id);
		}
		
		return $object;
	}
	
	protected function getTemplate() {
		$data		= $this->getData();
		$layout		= $this->getInvoiceDetails($data->ide)->invoice_layout;
		$prefix		= $this->getInvoiceDetails($data->ide)->invoice_prefix;
		$prefix		= trim($prefix) ? $prefix : '';
		$padding	= $this->getInvoiceDetails($data->ide)->invoice_padding;
		$title		= $this->getInvoiceDetails($data->ide)->invoice_title;
		$optionals	= array('{discount}' => '', '{tax}' => '', '{late_fee}' => '', '{early_fee}' => '');
		
		$placeholders = array(
			'{invoice_id}'		 => $prefix.str_pad($this->order_id, $padding, 0, STR_PAD_LEFT),
			'{email}' 			 => $data->email,
			'{name}' 			 => $data->name,
			'{username}' 		 => $data->idu ? JFactory::getUser($data->idu)->get('username') : '',
			'{payment}' 		 => $data->gateway ? rseventsproHelper::getPayment($data->gateway) : '-',
			'{date}'	 		 => rseventsproHelper::showdate($data->date),
			'{invoice_table}'	 => $this->createInvoiceTable($optionals),
			'{site_name}' 	     => JFactory::getConfig()->get('sitename'),
			'{site_url}' 	     => JUri::root()
		);
		
		$replace	= array_keys($placeholders);
		$with		= array_values($placeholders);
		$layout		= str_replace($replace, $with, $layout);
		$layout		= str_replace(array_keys($optionals), array_values($optionals), $layout);
		$title		= str_replace($replace, $with, $title);
		
		$this->document = new stdClass();
		$this->document->title = $title;
		$this->document->font = $this->getInvoiceDetails($data->ide)->invoice_font;
		$this->document->orientation = $this->getInvoiceDetails($data->ide)->invoice_orientation;
		
		return $layout;
	}
	
	protected function createInvoiceTable(&$optionals) {
		$total	 = 0;
		$data	 = $this->getData();
		$table	 = '';
		
		if ($folder = JUri::root(true))      {
			$site_path = substr(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_SITE), 0, -strlen($folder));
		} else {
			$site_path = str_replace(DIRECTORY_SEPARATOR, '/', JPATH_SITE);
		}
		
		if ($css_path = realpath($site_path.'/' . JHtml::stylesheet('com_rseventspro/invoice.css', array('pathOnly' => true, 'relative' => true)))) {
			$table .= '<link rel="stylesheet" href="'.$this->escape($css_path).'" type="text/css"/>';
		}
		
		$table .= '<table width="100%" cellpadding="3" cellspacing="1" class="invoice_table">
                <thead>
                    <tr>
                        <th class="package">'.JText::_('COM_RSEVENTSPRO_INVOICE_ITEM').'</th>
                        <th class="price">'.JText::_('COM_RSEVENTSPRO_INVOICE_PRICE').'</th>
                        <th class="quantity">'.JText::_('COM_RSEVENTSPRO_INVOICE_QTY').'</th>
                        <th class="total">'.JText::_('COM_RSEVENTSPRO_INVOICE_TOTAL').'</th>
                    </tr>
                </thead>
                <tbody>';
		
		if ($data->ide) {
			$tickets = $this->getTickets();
			
			$table .= '<tr><td colspan="4">'.JText::_('COM_RSEVENTSPRO_INVOICE_EVENT').' '.$this->getEventName($data->ide).'</td></tr>';
			foreach ($tickets as $ticket) {
				$total += $ticket->quantity * $ticket->price;
				
				$table .= '<tr>';
				$table .= '<td>'.$this->escape($ticket->id ? $ticket->name : JText::_('COM_RSEVENTSPRO_FREE_ENTRANCE')).'</td>';
				$table .= '<td>'.$this->escape(rseventsproHelper::currency($ticket->price)).'</td>';
				$table .= '<td>'.$this->escape($ticket->quantity).'</td>';
				$table .= '<td>'.$this->escape(rseventsproHelper::currency($ticket->quantity * $ticket->price)).'</td>';
				$table .= '</tr>';
			}
			
			$total = $total - $data->discount;
		} else {
			if ($this->cart) {
				$cart = $this->getCart();
				$events = $cart->getEvents();
				
				foreach ($events as $event) {
					$table .= '<tr><td colspan="4">'.JText::_('COM_RSEVENTSPRO_INVOICE_EVENT').' '.$this->getEventName($event).'</td></tr>';
					
					$tickets = $this->getCartTickets($event);
					
					if ($tickets) {
						foreach ($tickets as $ticket) {
							$table .= '<tr>';
							$table .= '<td>'.$this->escape($ticket->name).'</td>';
							$table .= '<td>'.$this->escape(rseventsproHelper::currency($ticket->total)).'</td>';
							$table .= '<td>'.$this->escape($ticket->quantity).'</td>';
							$table .= '<td>'.$this->escape(rseventsproHelper::currency($ticket->total * $ticket->quantity)).'</td>';
							$table .= '</tr>';
							
							if (isset($ticket->extra) && !empty($ticket->extra)) {
								foreach ($ticket->extra as $label => $price) {
									$table .= '<tr>';
									$table .= '<td>- '.$this->escape($label).'</td>';
									$table .= '<td>'.$this->escape(rseventsproHelper::currency($price)).'</td>';
									$table .= '<td>'.$this->escape(1).'</td>';
									$table .= '<td>'.$this->escape(rseventsproHelper::currency($price)).'</td>';
									$table .= '</tr>';
								}
							}
							
						}
					}
				}
				
				$total = $cart->total;
			}
		}
		
		if (!empty($data->early_fee)) {
			if ($data->ide) {
				$total = $total - $data->early_fee;
			}
			
			$optionals['{early_fee}'] = rseventsproHelper::currency($data->early_fee);
			
			$table .= '<tr>';
            $table .= '<td class="early_fee" colspan="3" align="right">'.JText::_('COM_RSEVENTSPRO_INVOICE_EARLY_FEE').'</td>';
			$table .= '<td class="early_fee">-'.$this->escape(rseventsproHelper::currency($data->early_fee)).'</td>';
			$table .= '</tr>';
		}
		
		if (!empty($data->late_fee)) {
			if ($data->ide) {
				$total = $total + $data->late_fee;
			}
			
			$optionals['{late_fee}'] = rseventsproHelper::currency($data->early_fee);
			
			$table .= '<tr>';
            $table .= '<td class="late_fee" colspan="3" align="right">'.JText::_('COM_RSEVENTSPRO_INVOICE_LATE_FEE').'</td>';
			$table .= '<td class="late_fee">'.$this->escape(rseventsproHelper::currency($data->late_fee)).'</td>';
			$table .= '</tr>';
		}
		
		if (!empty($data->tax)) {
			if ($data->ide) {
				$total = $total + $data->tax;
			}
			
			$optionals['{tax}'] = rseventsproHelper::currency($data->tax);
			
			$table .= '<tr>';
            $table .= '<td class="tax" colspan="3" align="right">'.JText::_('COM_RSEVENTSPRO_INVOICE_TAX').'</td>';
			$table .= '<td class="tax">'.$this->escape(rseventsproHelper::currency($data->tax)).'</td>';
			$table .= '</tr>';
		}
		
		if (!empty($data->discount)) {
			
			$optionals['{discount}'] = rseventsproHelper::currency($data->discount);
			
			$table .= '<tr>';
            $table .= '<td class="discount" colspan="3" align="right">'.JText::_('COM_RSEVENTSPRO_INVOICE_DISCOUNT').'</td>';
			$table .= '<td class="discount">-'.$this->escape(rseventsproHelper::currency($data->discount)).'</td>';
			$table .= '</tr>';
		}
		
		$table .= '<tr>
                        <td class="grand total" colspan="3" align="right">'.JText::_('COM_RSEVENTSPRO_INVOICE_GRAND_TOTAL').'</td>
                        <td class="grand total">'.$this->escape(rseventsproHelper::currency($total)).'</td>
                    </tr>
                </tbody>
            </table>';
		
		return $table;
	}
	
	protected function getTickets() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('ut.quantity'))->select($db->qn('t').'.*')
			->from($db->qn('#__rseventspro_user_tickets','ut'))
			->join('left',$db->qn('#__rseventspro_tickets','t').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
			->where($db->qn('ut.ids').' = '.$db->q($this->order_id));
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	protected function getCart() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('hash'))
			->from($db->qn('#__rseventspro_cart'))
			->where($db->qn('ids').' = '.$db->q($this->order_id));
		
		$db->setQuery($query);
		if ($hash = $db->loadResult()) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/cart.php';
			return RSEventsProCart::getInstance($hash);
		}
		
		return false;
	}
	
	protected function getCartTickets($event) {
		$cart	= $this->getCart();
		$return = array();
		
		if ($tickets = $cart->getTickets($event)) {
			foreach ($tickets as $ticket) {
				$extraCost 	= array_sum((array) $ticket->prices);
				$hash		= md5($ticket->ticketID.$extraCost);
				
				if (empty($return[$hash])) {
					$ticketInfo = new stdClass();
				
					$info					= $cart->getTicket($ticket->ticketID);
					$labels					= $cart->getFieldLabels($ticket->ticketID);
					$ticketInfo->name 		= $ticket->ticketID ? $info->name : JText::_('COM_RSEVENTSPRO_FREE_ENTRANCE');
					$ticketInfo->total 		= $ticket->ticketID ? $info->price : 0;
					$ticketInfo->quantity 	= 1;
					$ticketInfo->extraCost 	= $extraCost;
					
					if ($extraCost) {
						$ticketInfo->extra = array();
						
						foreach ($ticket->prices as $name => $price) {
							if (empty($price)) continue;
							$label = isset($labels[$name]) ? $labels[$name] : $name;
							$ticketInfo->extra[$label] = $price;
						}
					}
					
					$return[$hash] = $ticketInfo;
				} else {
					$return[$hash]->quantity++;
				}
			}
		}
		
		return $return;
	}
	
	protected function getCartEvents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('events'))
			->from($db->qn('#__rseventspro_cart'))
			->where($db->qn('ids').' = '.$db->q($this->order_id));
		
		$db->setQuery($query);
		$events = $db->loadResult();
		
		return explode(',', $events);
	}
	
	protected function getEventName($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'UTF-8');
	}
}