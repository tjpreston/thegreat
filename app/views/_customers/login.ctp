<?php
	$this->set('body_id', 'login');
?>

<div class="grid_24">

<h1><span class="face1">Please Login</span> <span class="face2">to Continue</span></h1>
<?php if (!empty($fromCheckout)): ?>
	<p>Please choose one of the following options to continue with your purchase.</p>
<?php else: ?>
	<p>New customers can register for a free account now. If you are an existing customer please log in.</p>
<?php endif; ?>

<?php

$url = array('action' => 'login');

if (isset($fromCheckout) && $fromCheckout) {
	$url['?'] = array('ref' => 'checkout');
}

?>
<?php echo $form->create('Customer', array('id' => 'login-form', 'url' => $url, 'class' => 'form block-label')); ?>
	
	<?php if (!empty($fromCheckout)): ?>
		<?php echo $form->hidden('to_checkout', array('value' => 1)); ?>
	<?php endif; ?>
	
	<div id="login-box" >
		<div id="login-column-new-customers" class="login-column grid_12 alpha">
			<div class="login-heading border-top-bottom"><span class="face1">New</span> <span class="face2">Customers</spam></div>
			<div class="login-content">
				<p>If you don't have a user account with The Great British Shop, you can create one here.</p>

				<?php $url = (!empty($fromCheckout)) ? '/checkout?register' : '/customers/register'; ?>
				
				<div class="new-customers grid_12 alpha omega">
					<div class="grid_6 alpha">
						<p>Register for your free user account to save time on future purchases.</p>
						<a href="<?php echo $url; ?>" class="dual"><span class="face1">Register</span> <span class="face2">New Account</span></a>
					</div>
					<?php if (!empty($fromCheckout)): ?>
						<div class="grid_6 omega">
							<p>Continue to checkout without creating a user account.</p>
							<a href="/checkout?guest" class="dual"><span class="face1">Checkout</span> <span class="face2">As Guest</span></a>
						</div>
					<?php endif;?>
				</div>

			</div>
		</div>
		
		<div id="login-column-registered-customers" class="login-column grid_12 omega">
			<div class="login-heading border-top-bottom"><span class="face1">Already</span> <span class="face2">Registered?</spam></div>
			<div class="login-content">
				<p>If you have an account with us, please log in:</p>

				<?php echo $session->flash('auth'); ?>

				<div class="no-required">
					<?php echo $form->input('email', array('class' => 'login-email', 'label' => 'E-mail', 'placeholder' => 'you@example.com')); ?>
					<?php echo $form->input('password', array('class' => 'login-password', 'label' => 'Password', 'placeholder' => '••••••••')); ?>
				</div>
				<p id="forgotten-your-password"><a href="/customers/forgotten_password">Forgotten your password?</a></p>

				<div id="login-actions">
					<?php echo $form->button('Login'); ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		
	</div>

<?php echo $form->end(); ?>

</div>
<div class="clear"></div>