fuel.controller.UserController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){
		//fuel.controller.BaseFuelController.prototype.add_edit.call(this);
		this._super();
		var sendEmailHTML = '<label for="send_email" id="send_email_notification">&nbsp; <input id="send_email" name="send_email" type="checkbox" value="1" /> ' + this.lang('form_label_send_email_notification') + '</lael>';

		
		$('#confirm_password').after(sendEmailHTML);

		$send_email_notification = $('#send_email_notification');
		$send_email = $('#send_email');
		$('#password,#confirm_password,#new_password').keyup(function(){
			var password = ($('#password').size()) ? $('#password').val() : $('#new_password').val();
			if (password != '' && password == $('#confirm_password').val()){
				$send_email_notification.show();
				$send_email.removeAttr('disabled');
			} else {
				$send_email_notification.hide();
				$send_email.attr('disabled', 'disabled');
			}
		});
		
		// trigger keyup initially just in case the values are the same
		$('#password,#confirm_password,#new_password').keyup();
		
		// toggle on off
		var toggleHTML = ' &nbsp; <input id="toggle_perms" name="toggle_perms" type="checkbox" value="1" class="float_right"/>';
		$('td.section h3').append(toggleHTML);
		var $perms = $('input:checkbox').not('#send_email, #toggle_perms');

		$('#toggle_perms').click(function() { 
		    $perms.attr('checked',$(this).is(':checked')); 
		 });
		
		var toggleAllPerms = function(){
			if ($perms.size() != $perms.filter(':checked').size()){
				$('#toggle_perms').removeAttr('checked'); 
			} else {
				$('#toggle_perms').attr('checked',true); 
			}
		}
		
		$perms.click(function(i){
			toggleAllPerms();
		})
		toggleAllPerms();
		
		
		
		
		$('.perms_list li input').click(function(e){
			$ul = $(this).parent().find('ul');
			if ($ul.length){
				if ($ul.css('display') == 'none'){
					$ul.slideDown('fast');
					$inputs = $ul.find('input');
					if (!$(':checked', $inputs).length){
						$inputs.prop('checked', true);
					}
				} else {
					$ul.slideUp('fast');
					$ul.find('input').prop('checked', false);
				}
			}
		});
		
		$('.perms_list li input').not(':checked').parent().find('ul').hide();
	
	}
	
});