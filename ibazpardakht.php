<?php 
/*
* 2013 Presta-Shop.ir
*
* Do not edit or remove author copyright
* if you have any problem contact us at iPresta.ir
*
*  @author Danoosh Miralayi - iPresta.ir
*  @copyright  2014-2015 iPresta.ir
*  نکته مهم:
*  حذف یا تغییر این اطلاعات به هر شکلی ممنوع بوده و پیگرد قانونی دارد
*/

class iBazPardakht extends PaymentModule
{  
	private $_html = '';

	private  $_post_url = 'http://bazpardakht.com/webservice/index.php';
	private $_go_url = 'http://bazpardakht.com/webservice/go.php';

	public function __construct(){  
		$this->name = 'ibazpardakht';  
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
		$this->bootstrap = true;
		$this->author = 'iPresta.ir';

		$this->currencies = true;
  		$this->currencies_mode = 'checkbox';

		parent::__construct();  		
		$this->context = Context::getContext();
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Bazpardakht Payment');  
		$this->description = $this->l('A free module to pay online.');  
		$this->confirmUninstall = $this->l('Are you sure, you want to delete your details?');
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');
		$config = Configuration::getMultiple(array('IPRESTA_BAZPARDAKHT_UserName', 'IPRESTA_BAZPARDAKHT_UserPassword'));			
		if (!isset($config['IPRESTA_BAZPARDAKHT_UserName']))
			$this->warning = $this->l('Your BazPardakht username must be configured in order to use this module');
		if (!isset($config['IPRESTA_BAZPARDAKHT_UserPassword']))
			$this->warning = $this->l('Your BazPardakht password must be configured in order to use this module');

	}  
	public function install(){
		if (!parent::install()
	    	OR !Configuration::updateValue('IPRESTA_BAZPARDAKHT_USER', '')
			OR !Configuration::updateValue('IPRESTA_BAZPARDAKHT_PASSWORD', '')
			OR !Configuration::updateValue('IPRESTA_BAZPARDAKHT_TEST', 0)
            OR !Configuration::updateValue('IPRESTA_BAZPARDAKHT_DEBUG', 0)
	      	OR !$this->registerHook('payment')
	      	OR !$this->registerHook('paymentReturn')){
			    return false;
		}else{
		    return true;
		}
	}
	public function uninstall(){
		if (!Configuration::deleteByName('IPRESTA_BAZPARDAKHT_USER') 
			OR !Configuration::deleteByName('IPRESTA_BAZPARDAKHT_PASSWORD')
            OR !Configuration::deleteByName('IPRESTA_BAZPARDAKHT_TEST')
			OR !Configuration::deleteByName('IPRESTA_BAZPARDAKHT_DEBUG')
			OR !parent::uninstall())
			return false;
		return true;
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('User Name'),
						'name' => 'IPRESTA_BAZPARDAKHT_USER',
						'class' => 'fixed-width-lg',
					),
					array(
						'type' => 'password',
						'label' => $this->l('Password'),
						'name' => 'IPRESTA_BAZPARDAKHT_PASSWORD',
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Enable Debug Mode'),
						'name' => 'IPRESTA_BAZPARDAKHT_DEBUG',
						'class' => 'fixed-width-xs',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBazPardakht';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'IPRESTA_BAZPARDAKHT_USER' => Tools::getValue('IPRESTA_BAZPARDAKHT_USER', Configuration::get('IPRESTA_BAZPARDAKHT_USER')),
			'IPRESTA_BAZPARDAKHT_PASSWORD' => Tools::getValue('IPRESTA_BAZPARDAKHT_PASSWORD', Configuration::get('IPRESTA_BAZPARDAKHT_PASSWORD')),
			'IPRESTA_BAZPARDAKHT_DEBUG' => Tools::getValue('IPRESTA_BAZPARDAKHT_DEBUG', (bool)Configuration::get('IPRESTA_BAZPARDAKHT_DEBUG')),
		);
	}



	public function copyRight()
	{
		$this->_html .= '

		<center><input type="submit" name="submitCheck" value="'.$this->l('بررسی امکان اتصال به وب سرویس').'" class="button" />
		<p style="text-align:center;">این عمل ممکن است مدتی طول بکشد. شکیبا باشید.</p></center>
		</fieldset></form>
		<p></p>
		<fieldset>		
		<legend>اطلاعات</legend>
		<p><a href="http://presta-shop.ir/forum/Thread-2487.html"> + پشتیبانی در انجمن</a></p>
		<p> + کپی رایت : <a href="http://presta-shop.ir">پرستاشاپ پارسی</a></p>
		<p> + نویسنده: دانوش میرعلایی مطلق</p>
		</fieldset>
		';
	}


    public function getContent()
	{
		$output = '';
		$errors = array();
		if (isset($_POST['submitBazPardakht']))
		{
			if (empty($_POST['IPRESTA_BAZPARDAKHT_USER']))
				$errors[] = $this->l('Your username is required.');

			if (empty($_POST['IPRESTA_BAZPARDAKHT_PASSWORD']))
				$errors[] = $this->l('Your password is required.');
			if (!count($errors))
			{
				Configuration::updateValue('IPRESTA_BAZPARDAKHT_USER', $_POST['IPRESTA_BAZPARDAKHT_USER']);
				Configuration::updateValue('IPRESTA_BAZPARDAKHT_PASSWORD', $_POST['IPRESTA_BAZPARDAKHT_PASSWORD']);
    //            Configuration::updateValue('IPRESTA_BAZPARDAKHT_TEST', $_POST['IPRESTA_BAZPARDAKHT_TEST']);
				Configuration::updateValue('IPRESTA_BAZPARDAKHT_DEBUG', $_POST['IPRESTA_BAZPARDAKHT_DEBUG']);
				$output = $this->displayConfirmation($this->l('Your settings have been updated.'));
			}
			else
				$output = $this->displayError(implode('<br />', $errors));
		}
		return $output.$this->renderForm();
	}
	
	public function prePayment()
	{
					
		$purchase_currency = new Currency(Currency::getIdByIsoCode('IRR'));
		$current_currency = new Currency($this->context->cookie->id_currency);			
		if($current_currency->id == $purchase_currency->id)
			$PurchaseAmount= number_format($this->context->cart->getOrderTotal(true, 3), 0, '', '');		 
		else
			$PurchaseAmount= number_format($this->convertPriceFull($this->context->cart->getOrderTotal(true, 3), $current_currency, $purchase_currency), 0, '', '');

		$additionalData = "Cart Number: ".$this->context->cart->id." Customer ID: ".$this->context->cart->id_customer;
		$params = array(
					'terminalId' =>  Configuration::get('Bank_Mellat_TerminalId'),
					'userName' => Configuration::get('Bank_Mellat_UserName'),
					'userPassword' => Configuration::get('Bank_Mellat_UserPassword'),
					'orderId' => ($this->context->cart->id).date('YmdHis'),
					'amount' => (int)$PurchaseAmount,
					'callBackUrl' => $this->context->link->getModuleLink('bankmellat', 'validation'),
					'localDate' => date('Ymd'),
					'localTime' => date("His"),
					'additionalData' => $additionalData,
					'payerId' => 0
				  );

		$res = $soapclient->call('bpPayRequest', $params, $this->_namespace);

		if ($soapclient->fault OR $err = $soapclient->getError())
		{
			$this->_postErrors[] = $this->l('Could not connect to bank or service.');
			$this->displayErrors();
			return $this->_postErrors;
		} 
		else
		{
			// Display the result
			if (is_array($res))
				$ress = explode (',',$res['return']);
			else
				$ress = explode (',',$res);
			$ResCode = $ress[0];
			$RefId     = $ress[1];
			if ($ResCode == "0")
			{
				$this->context->cookie->__set("RefId", $RefId);
				$this->context->cookie->__set("amount", (int)$PurchaseAmount);

				$this->context->smarty->assign(array(
					'redirect_link' => $this->link,
					'ref_id' => $RefId
				));
				return true;
			} 
			else {
				$this->showMessages($ResCode);
				return $this->_postErrors;
			}

		}
			
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$this->_post_url);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"id=".$id."&amount=".$amount."&callbac
		k=".$callback."&resnum=".$resnum);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$res = curl_exec($ch);curl_close($ch);
		return $res;

	}

	public function verify($saleOrderId,$saleReferenceId,$soapclient = NULL)
	{
		if(!$soapclient)
		{	
			include_once('lib/nusoap.php');
			$soapclient = new nusoap_client($this->webservice,'wsdl');
		}

		if (!$soapclient)
		{
			$this->_postErrors[] = $this->l('اتصال به بانک برقرار نشد');
			// if(!empty($err))
				// $this->_postErrors[] = $err;
			return $this->_postErrors;
			// return $return;
		}

		// Params For Verify
		$params = array(
			'terminalId' =>  Configuration::get('Bank_Mellat_TerminalId'),
			'userName' => Configuration::get('Bank_Mellat_UserName'),
			'userPassword' => Configuration::get('Bank_Mellat_UserPassword'),
			'orderId' => ($this->context->cart->id).date('YmdHis'),
			'saleOrderId' => $saleOrderId,
			'saleReferenceId' => $saleReferenceId
		);

		$result = $soapclient->call('bpVerifyRequest', $params, $this->_namespace);

		if ($soapclient->fault OR $err = $soapclient->getError())
		{
			$this->_postErrors[] = $this->l('Could not connect to bank or service.');
			return $this->_postErrors;
		} 
		if ($result['return'] != "0"){
			$this->showMessages($result['return']);
			return $this->_postErrors;
		}
		return true;
	}

	public function settle($saleOrderId,$saleReferenceId, $soapclient = NULL)
	{
		if(!$soapclient)
		{	
			include_once('lib/nusoap.php');
			$soapclient = new nusoap_client($this->webservice,'wsdl');
		}

		if (!$soapclient)
		{
			$this->_postErrors[] = $this->l('اتصال به بانک برقرار نشد');
			// if(!empty($err))
				// $this->_postErrors[] = $err;
			return $this->_postErrors;
			// return $return;
		}

		//Params for settle
		$params = array(
			'terminalId' =>  Configuration::get('Bank_Mellat_TerminalId'),
			'userName' => Configuration::get('Bank_Mellat_UserName'),
			'userPassword' => Configuration::get('Bank_Mellat_UserPassword'),
			'orderId' => ($this->context->cart->id).date('YmdHis'),
			'saleOrderId' => $saleOrderId,
			'saleReferenceId' => $saleReferenceId
		);

		$result = $soapclient->call('bpSettleRequest', $params, $this->_namespace);
		if ($soapclient->fault OR $err = $soapclient->getError())
		{
			$this->_postErrors[] = $this->l('Could not connect to bank or service.');
			return $this->_postErrors;
		} 


		if ($result['return'] != "0"){
			$this->showMessages($result['return']);
			return $this->_postErrors;
			//return $return;
		}
		return true;
	}

	public function inquiry($saleOrderId,$saleReferenceId, $soapclient =NULL)
	{
		if(!$soapclient)
		{	include_once('lib/nusoap.php');
			$soapclient = new nusoap_client($this->webservice,'wsdl');
		}

		if (!$soapclient)
		{
			$this->_postErrors[] = $this->l('اتصال به بانک برقرار نشد');
			// if(!empty($err))
				// $this->_postErrors[] = $err;
			return $this->_postErrors;
			// return $return;
		}

		//Params for inquiry
		$params = array(
			'terminalId' =>  Configuration::get('Bank_Mellat_TerminalId'),
			'userName' => Configuration::get('Bank_Mellat_UserName'),
			'userPassword' => Configuration::get('Bank_Mellat_UserPassword'),
			'orderId' => ($this->context->cart->id).date('YmdHis'),
			'saleOrderId' => $saleOrderId,
			'saleReferenceId' => $saleReferenceId
		);

		$result = $soapclient->call('bpInquiryRequest', $params, $this->_namespace);
		if ($soapclient->fault OR $err = $soapclient->getError())
		{
			$this->_postErrors[] = $this->l('Could not connect to bank or service.');
			return $this->_postErrors;
		} 

		if ($result['return'] != "0"){
			$this->showMessages($result['return']);
			return $this->_postErrors;
		}
		return true;
	}

	public function reverse($saleOrderId,$saleReferenceId, $soapclient = NULL)
	{
		if(!$soapclient)
		{	include_once('lib/nusoap.php');
			$soapclient = new nusoap_client($this->webservice,'wsdl');
		}

		if (!$soapclient)
		{
			$this->_postErrors[] = $this->l('اتصال به بانک برقرار نشد');
			// if(!empty($err))
				// $this->_postErrors[] = $err;
			return $this->_postErrors;
			// return $return;
		}

		//Params for reversal
		$params = array(
			'terminalId' =>  Configuration::get('Bank_Mellat_TerminalId'),
			'userName' => Configuration::get('Bank_Mellat_UserName'),
			'userPassword' => Configuration::get('Bank_Mellat_UserPassword'),
			'orderId' => ($this->context->cart->id).date('YmdHis'),
			'saleOrderId' => $saleOrderId,
			'saleReferenceId' => $saleReferenceId
		);

		$result = $soapclient->call('bpReversalRequest', $params, $this->_namespace);
		if ($soapclient->fault OR $err = $soapclient->getError())
		{
			$this->_postErrors[] = $this->l('Could not connect to bank or service.');
			return $this->_postErrors;
		} 

		if ($result['return'] != "0"){
			$this->showMessages($result['return']);
			return $this->_postErrors;
		}
		return true;
	}

	public function showMessages($result)
	{                
		switch($result)
		{ 
			case 0:  $this->_postErrors[]=$this->l('تراکنش با موفقیت انحام شد'); break;
			case 11: $this->_postErrors[]=$this->l('شماره کارت نامعتبر است'); break;
			case 12: $this->_postErrors[]=$this->l('موجودی کافی نیست'); break;
			case 13: $this->_postErrors[]=$this->l('رمز نادرست است'); break;  
			case 14: $this->_postErrors[]=$this->l('تعداد دفعات وارد کردن رمز بیش از حد مجاز است'); break;    
			case 15: $this->_postErrors[]=$this->l('کارت نامعتبر است'); break;
			case 16: $this->_postErrors[]=$this->l('دفعات برداشت وجه بیش از حد مجاز است'); break;
			case 17: $this->_postErrors[]=$this->l('کاربر از انجام تراکنش منصرف شده است'); break;
			case 18: $this->_postErrors[]=$this->l('تاریخ انقضای کارت گذشته است'); break;
			case 19: $this->_postErrors[]=$this->l('مبلغ برداشت وجه بیش از حد مجاز است'); break;
			case 111: $this->_postErrors[]=$this->l('صادر کننده کارت نامعتبر است'); break;
			case 112: $this->_postErrors[]=$this->l('خطای سوییچ صادر کننده کارت'); break;
			case 113: $this->_postErrors[]=$this->l('پاسخی از صادر کننده کارت دریافت نشد'); break;
			case 114: $this->_postErrors[]=$this->l('دارنده کارت مجاز به انجام این تراکنش نیست'); break;
			case 21: $this->_postErrors[]=$this->l('پذیرنده نامعتبر است'); break;
			case 23: $this->_postErrors[]=$this->l('خطای امنیتی رخ داده است'); break;
			case 24: $this->_postErrors[]=$this->l('اطلاعات کاربری پذیرنده نامعتبر است'); break;
			case 25: $this->_postErrors[]=$this->l('مبلغ نامعتبر است'); break;
			case 31: $this->_postErrors[]=$this->l('پاسخ نامعتبر است'); break;
			case 32: $this->_postErrors[]=$this->l('فرمت اطلاعات وارد شده صحیح نمی باشد'); break;
			case 33: $this->_postErrors[]=$this->l('حساب نامعتبر است'); break;
			case 34: $this->_postErrors[]=$this->l('خطای سیستمی'); break;
			case 35: $this->_postErrors[]=$this->l('تاریخ نامعتبر است'); break;
			case 41: $this->_postErrors[]=$this->l('شماره درخواست تکراری است'); break;
			case 42: $this->_postErrors[]=$this->l('تراکنش Sale یافت نشد'); break;
			case 43: $this->_postErrors[]=$this->l('قبلا درخواست Verify داده شده است'); break;
			case 44: $this->_postErrors[]=$this->l('درخواست Verify یافت نشد'); break;
			case 45: $this->_postErrors[]=$this->l('تراکنش Settle شده است'); break;
			case 46: $this->_postErrors[]=$this->l('تراکنش Settle نشده است'); break;
			case 47: $this->_postErrors[]=$this->l('تراکنش Settle یافت نشد'); break;
			case 48: $this->_postErrors[]=$this->l('تراکنش Reverse شده است'); break;
			case 49: $this->_postErrors[]=$this->l('تراکنش Refund یافت شند'); break;
			case 412: $this->_postErrors[]=$this->l('شناسه قبض نادرست است'); break;
			case 413: $this->_postErrors[]=$this->l('شناسه پرداخت نادرست است'); break;
			case 414: $this->_postErrors[]=$this->l('سازمان صادر کننده قبض نامعتبر است'); break;
			case 415: $this->_postErrors[]=$this->l('زمان جلسه کاری به پایان رسیده است'); break;
			case 416: $this->_postErrors[]=$this->l('خطا در ثبت اطلاعات'); break;
			case 417: $this->_postErrors[]=$this->l('شناسه پرداخت کننده نامعتبر است'); break;
			case 418: $this->_postErrors[]=$this->l('اشکال در تعریف اطلاعات مشتری'); break;
			case 419: $this->_postErrors[]=$this->l('تعداد دفعات ورود اطلاعات از حد مجاز گذشته است'); break;
			case 421: $this->_postErrors[]=$this->l('IP نامعتبر است'); break;
			case 51: $this->_postErrors[]=$this->l('تراکنش تکراری است'); break;
			case 54: $this->_postErrors[]=$this->l('تراکنش مرجع موجود نیست'); break;
			case 55: $this->_postErrors[]=$this->l('تراکنش نامعتبر است'); break;
			case 61: $this->_postErrors[]=$this->l('خطا در واریز'); break;
			}
		return $this->_postErrors;
	}

	// to show only one error
	public function showErrorMessages($result)
	{
		$Message = $this->showMessages($result);
		$this->_html = '';
		$this->_postErrors = array();
		return $Message;
	}

	public function hookPayment($params){
		if (!$this->active)
			return ;
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		return ;
	}

	/**
	 *
	 * @return float converted amount from a currency to an other currency
	 * @param float $amount
	 * @param Currency $currency_from if null we used the default currency
	 * @param Currency $currency_to if null we used the default currency
	 */
	public static function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null)
	{
		if ($currency_from === $currency_to)
			return $amount;
		if ($currency_from === null)
			$currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		if ($currency_to === null)
			$currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT'))
			$amount *= $currency_to->conversion_rate;
		else
		{
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
			// Convert amount to default currency (using the old currency rate)
			$amount = Tools::ps_round($amount / $conversion_rate, 2);
			// Convert to new currency
			$amount *= $currency_to->conversion_rate;
		}
		return Tools::ps_round($amount, 2);
	}
}