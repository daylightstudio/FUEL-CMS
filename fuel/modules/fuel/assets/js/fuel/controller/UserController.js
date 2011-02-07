fuel.controller.UserController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){
		fuel.controller.BaseFuelController.prototype.add_edit.call(this);
		var sendEmailHTML = '<span id="send_email_notification">&nbsp; <input id="send_email" name="send_email" type="checkbox" value="1" /> ' + this.lang('form_label_send_email_notification') + '</span>';
		
		$('#confirm_password').after(sendEmailHTML);

		$send_email_notification = $('#send_email_notification');
		$send_email = $('#send_email');
		$('#confirm_password,#new_password').keyup(function(){
			if ($('#new_password').val() != '' && $('#new_password').val() == $('#confirm_password').val()){
				$send_email_notification.show();
				$send_email.removeAttr('disabled');
			} else {
				$send_email_notification.hide();
				$send_email.attr('disabled', 'disabled');
			}
		})
		$('#confirm_password,#new_password').keyup();
		
		// toggle on off
		var toggleHTML = ' &nbsp; <input id="toggle_perms" name="toggle_perms" type="checkbox" value="1" class="float_right"/>';
		$('td.section h3').append(toggleHTML);
		var $perms = $('input:checkbox').not('#send_email, #toggle_perms');

		$('#toggle_perms').click(function() { 
		    $perms.attr('checked',$(this).is(':checked')); 
		 });
		
		var togglePerms = function(){
			if ($perms.size() != $perms.filter(':checked').size()){
				$('#toggle_perms').removeAttr('checked'); 
			} else {
				$('#toggle_perms').attr('checked',true); 
			}
		}
		
		$perms.click(function(i){
			togglePerms();
		})
		togglePerms();
	}
	
});