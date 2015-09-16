<?php
echo $this->Html->css('quick_order.css', null, array('inline' => false));
echo $this->Html->script('quick_order.js', array('inline' => false));
?>

<div id="leftcol">
	<?php
		echo $this->element('template/account_nav_panel');
		echo $this->element('catalog/featured_and_recent');
	?>
</div>

<div id="content">

	<div class="header">
		<h1>Quick Order Form</h1>
		<p class="intro">Know exactly what you're looking for? Quickly build your order here.</p>
	</div>

	<div class="content-pad">

		<div id="quick-order">
			
			<div id="add-prod">
				<h2>Add Product</h2>
				<form class="horizontal-form">
					<div class="input text">
						<label for="prod-code">Product Code</label>
						<input id="prod-code" type="text" placeholder="e.g. 17049/B19" />
					</div>
					<div class="input text">
						<label for="prod-qty">Quantity</label>
						<input id="prod-qty" type="number" placeholder="1" value="1" min="1" step="1" class="small" />
					</div>
					<div class="input text" id="result" style="display:none">
						<label>Result</label>
						<div id="prod-preview"></div>
					</div>
					<div class="input text" id="add-to-basket" style="display:none">
						<label>&nbsp;</label>
						<button id="save" class="btn">Add to Order</button>
					</div>
				</form>
			</div>
			
			
			<div id="quick-basket">
				<h2>Your Order</h2>
				<form action="/basket/add" method="post">
					<input type="hidden" name="data[Basket][redirect_to]" value="/basket" />
					<table id="basket">
						<thead>
							<tr>
								<th>Product</th>
								<th style="width: 66px;">Price</th>
								<th style="width: 66px;">Qty</th>
								<th style="width: 66px;">Total</th>
								<th style="width: 66px;">&nbsp;</th>
							</tr>
						</thead>
						<tbody id="prods">

						</tbody>
					</table>
					<input type="image" id="quick-buy" src="/img/buttons/continue-secure.gif" alt="Continue..." />
				</form>
			</div>
			
		</div>

	</div>
	
</div>


