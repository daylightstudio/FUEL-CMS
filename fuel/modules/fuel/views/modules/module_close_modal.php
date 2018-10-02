<script type="text/javascript">
	if (top.window.fuel && top.window.fuel.closeModal){
		top.window.fuel.closeModal();
	}
	if (top.window.fuel && top.window.fuel.setNotification){
		top.window.fuel.setNotification(' &nbsp; ', 'warning');

	// for inline editing on the front end we just refresh the page
	} else if (top.window != window){
		top.window.location = window.location;
	}
</script>