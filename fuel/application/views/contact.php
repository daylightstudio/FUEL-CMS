<h1>Contact Us</h1>
<?php if ($this->session->flashdata('success')) : ?>
<p class="success">Thank you for contacting us. We will get back to you shortly.</p>

<?php else : ?>

<p>Please fill out the form below to send us any questions:</p>
<?php echo $form; ?>
<?php endif; ?>