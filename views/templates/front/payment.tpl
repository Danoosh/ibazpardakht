{*
* 2014-2015 iPresta.ir
*
*
*  @author iPresta.ir - Danoosh Miralayi
*  @copyright  2014-2015 iPresta.ir
*}




<div class="block-center" id="">
<h2>{l s='Pay by BazPardakht' mod='ibazpardakht'}</h2>

{include file="$tpl_dir./errors.tpl"}

{if isset($prepay) && $prepay}
	<br />
	<p>{l s='Connecting to gateway' mod='ibazpardakht'}...</p>
	<p>{l s='If there is problem on redirectiong click on payment button bellow' mod='ibazpardakht'}</p>
	<script type="text/javascript">
		setTimeout("document.forms.frmpayment.submit();",10);
	</script>
	<form name="frmpayment" action="{$redirect_link}?id={$ref_id}" method="post">
		<input type="hidden" id="id" name="id" value="{$ref_id}" />
		<input type="submit" class="button" value="{l s='Payment' mod='ibazpardakht'}" />
	</form>
	<p></p>
{/if}
</div>