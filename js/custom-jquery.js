jQuery(document).ready(function(){
	/**
	* Added timepicker JS
	*/
	jQuery('.timepicker').timepicker({
		timeFormat: 'H:mm',
		interval: 30,
		startTime: '00:00',
		dynamic: false,
		dropdown: true,
		scrollbar: true
	});
	/**
	* Set readonly jquery on load
	*/
	jQuery('.woch_web_status option:selected').each(function() {
	    if(jQuery(this).val() == 'custom')
	    {
	      jQuery(this).closest('td').next('td').removeClass('not-allowed-wrapper').find('input').attr('readonly', false).css('pointer-events','');
          jQuery(this).closest('td').next('td').removeClass('not-allowed-wrapper').next('td').find('input').attr('readonly', false).css('pointer-events','');
	    }
	    else
	    {
	      jQuery(this).closest('td').next('td').addClass('not-allowed-wrapper').find('input').attr('readonly', true).css('pointer-events','none');
          jQuery(this).closest('td').next('td').next('td').addClass('not-allowed-wrapper').find('input').attr('readonly', true).css('pointer-events','none');
	    }	
    });
	/**
	* Set readonly jquery on change
	*/
	jQuery(document).on('change','.woch_web_status',function() {
		var selectedVal = jQuery('option:selected', this).val();
        if(selectedVal == "full_open" || selectedVal == "full_close")
        {
          jQuery(this).closest('td').next('td').addClass('not-allowed-wrapper').find('input').attr('readonly', true).css('pointer-events','none');
          jQuery(this).closest('td').next('td').next('td').addClass('not-allowed-wrapper').find('input').attr('readonly', true).css('pointer-events','none');
        }
        else
        {
          jQuery(this).closest('td').next('td').removeClass('not-allowed-wrapper').find('input').attr('readonly', false).css('pointer-events','');
          jQuery(this).closest('td').next('td').next('td').removeClass('not-allowed-wrapper').find('input').attr('readonly', false).css('pointer-events','');
        }
    });
	/**
	* Added timepicker validations
	*/
	jQuery(".timepicker").on("change", function(){
		var validTime = jQuery(this).val().match(/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i);
		if (!validTime) 
		{
			jQuery(this).val('').focus();
			alert("You entered wrong time...");
		} 
		else 
		{
			jQuery(this).css('background', 'white');
		}
	});
	function scroll_to_top(){
	   var scroll_pos=(0);          
	   jQuery('.website_error_message.error').css('display','block');
	   jQuery('html, body').animate({scrollTop:(scroll_pos)}, '2500');
	}
	/**
	* AJAX to validate timeintervals
	*/
	jQuery('#savetime').on('click',function(){
		jQuery('.website_error_message.error').css('display','none');
		var form = jQuery("#ajx_opncloseform").serialize();
		jQuery.ajax({ 
			data: {action: 'gwl_saveopen_close_timehours',data:form},
			type: 'post',
			url: ajaxurl,
			success: function(data) 
			{
				if(data.mon=='false')
				{
					jQuery("#monday").html("Please select correct time");
					jQuery("#monday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#monday").html("");
					jQuery("#monday").removeClass("active");
				}
				if(data.tue=='false')
				{
					jQuery("#tuesday").html("Please select correct time");
					jQuery("#tuesday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#tuesday").html("");
					jQuery("#tuesday").removeClass("active");
				}
				if(data.wed=='false')
				{
					jQuery("#wednesday").html("Please select correct time");
					jQuery("#wednesday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#wednesday").html("");
					jQuery("#wednesday").removeClass("active");
				}
				if(data.thus=='false')
				{
					jQuery("#thursday").html("Please select correct time");
					jQuery("#thursday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#thursday").html("");
					jQuery("#thursday").removeClass("active");
				}
				if(data.fri=='false')
				{
					jQuery("#friday").html("Please select correct time");
					jQuery("#friday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#friday").html("");
					jQuery("#friday").removeClass("active");
				}
				if(data.sat=='false')
				{
					jQuery("#saturday").html("Please select correct time");
					jQuery("#saturday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#saturday").html("");
					jQuery("#saturday").removeClass("active");
				}
				if(data.sun=='false')
				{
					jQuery("#sunday").html("Please select correct time");
					jQuery("#sunday").addClass("active");
					scroll_to_top();
				}
				else
				{
					jQuery("#sunday").html("");
					jQuery("#sunday").removeClass("active");

				}

				if(data.red_type=='false')
				{
					jQuery("#redirection_required").html("Please select Url/Page for redirection");
					jQuery("#redirection_required").css("color", "red");
				}
				else
				{
					jQuery("#redirection_required").html("");
				}

				if(data.same_url=='false')
				{
					jQuery("#same_url_msg").html("Same domain URL's are not allowed!");
					jQuery("#same_url_msg").css("color", "red");
				}
				else
				{
					jQuery("#same_url_msg").html("");
				}

				if(data.invalid_url=='false')
				{
					jQuery("#invalid_url_msg").html("Invalid URL!");
					jQuery("#invalid_url_msg").css("color", "red");
				}
				else
				{
					jQuery("#invalid_url_msg").html("");
				}

				if(data =="")
				{	
					jQuery("#sunday").html("");
					jQuery("#saturday").html("");
					jQuery("#friday").html("");
					jQuery("#thursday").html("");
					jQuery("#wednesday").html("");
					jQuery("#tuesday").html("");
					jQuery("#monday").html("");
					jQuery("#updated").html("<h2 style='color:#4CAF50'>Settings updated succesfully...</h2>");
					jQuery("#updated").css("color", "red");
					setTimeout(function(){ jQuery("#updated").html(""); }, 3000);
				}
			}
		});
	});

	jQuery('ul#redirection_options li').click(function(){		
		jQuery('ul#redirection_options li').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('form#ajx_opncloseform .redirect_option_Section').removeClass('active_section');
		var redirect_option = jQuery(this).attr('for');
		jQuery('input#redirect_type').val(redirect_option);
		jQuery('form#ajx_opncloseform #'+redirect_option).addClass('active_section');
	});
});
jQuery('body').click(function(){
 	jQuery("html, body").scrollTop(50);
});