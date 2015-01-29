{l s='Your order on %s is complete.' sprintf=$shop_name mod='ibazpardakht'}
		{if !isset($reference)}
			<br /><br />{l s='Your order number' mod='ibazpardakht'}: {$id_order}
		{else}
			<br /><br />{l s='Your order number' mod='ibazpardakht'}: {$id_order}
			<br /><br />{l s='Your order reference' mod='ibazpardakht'}: {$reference}
		{/if}		<br /><br />{l s='An email has been sent with this information.' mod='ibazpardakht'}
		<br /><br /> <strong>{l s='Your order will be sent as soon as posible.' mod='ibazpardakht'}</strong>
		<br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='ibazpardakht'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='ibazpardakht'}</a>.
	</p><br />