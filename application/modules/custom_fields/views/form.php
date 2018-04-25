<style>
#tbl-records label.checkbox {
    text-align: left;
}
</style>
<?php echo form_open('custom_fields/doSave/'.$info->custom_field_id,'class="smart-form" id="custom_fields-form"');?>
   
	<fieldset> 
		<section class="row">
			<label for="custom_field_label">Table</label>
			<label class="select">
				<select name="custom_field_table" id="custom_field_table" tabindex="1">
                    <option value=""></option>
                    <?php 
                    $custom_field_tables = array('users' => 'User');
                    foreach ($custom_field_tables as $table => $label) { ?>
                    <option value="<?php echo $table; ?>" <?php if ($info->custom_field_table == $table) { ?>selected="selected"<?php } ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
                <i></i>
			</label>
		</section>

		<section class="row">
			<label for="custom_field_label">Label</label>
			<label class="input">
				<input type="text" name="custom_field_label" id="custom_field_label" value="<?php echo set_value('custom_field_label', $info->custom_field_label);?>" placeholder="Label" tabindex="2">
				
			</label>
		</section>
			
		<section class="row">
			<label for="custom_field_type">Type</label>
			<label class="input">
				<input type="text" name="custom_field_type" id="custom_field_type" value="<?php //echo set_value('custom_field_type', $info->custom_field_type);?>" placeholder="Type" tabindex="3">
				
			</label>
		</section>

		<section class="row">
			<label for="custom_field_sort">Sort</label>
			<label class="input">
				<input type="text" name="custom_field_sort" id="custom_field_sort" value="<?php //echo set_value('role_name', $info->custom_field_sort);?>" placeholder="Sort" tabindex="4">
				
			</label>
		</section>
		

		<button type="submit" id="submit" class="btn btn-primary btn-sm">Submit</button>
	</fieldset>
</form>
  
<script type="text/javascript">
    var BASE_URL = '<?php echo base_url();?>';

    runAllForms();
	
	$('input[type=radio]').on('change', function(e) {
		var val = $("input[name='option']:checked").val();
		var url = '<?php echo site_url();?>course/'+val;
		history.pushState(null, null, url);
		checkURL();

		var title = $("input[name='option']:checked").parent().text();

		// change page title from global var
		document.title = (title || document.title);
		
		e.preventDefault();
		
	});
    var validatefunction = function() {

        $("#custom_fields-form").validate({
            // Rules for form validation
            rules : {
                custom_field_table : {
                    required : true
                },
                custom_field_label : {
                    required : true,
                    maxlength: 150
                }
            },

            // Messages for form validation
            messages : {
                custom_field_table : {
                    required : '<i class="fa fa-times-circle"></i> Please select table'
                },
                custom_field_label : {
                    required : '<i class="fa fa-times-circle"></i> Please add label',
                    maxlength: '<i class="fa fa-times-circle"></i> The label can not exceed 150 characters in length.'
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                }else{
                    error.insertAfter(element);
                }
            },
            // Ajax form submition
            submitHandler : function(form) {
                
                $(form).ajaxSubmit({
                    beforeSend: function () {
                        $('#submit').html('Please wait...');
                        $('#submit').attr("disabled", "disabled");
                    },
                    success:function(response)
                    {
                        if(response)
                        {
                            $.smallBox({
                                title : "Success",
                                content : response.message,
                                color : "#739E73",
                                iconSmall : "fa fa-check",
                                timeout : 3000
                            });
                            $('.bootbox-close-button').trigger('click');
                            checkURL();
                        }
                        else
                        {
                            $.smallBox({
                                title : "Error",
                                content : response.message,
                                color : "#C46A69",
                                iconSmall : "fa fa-warning shake animated",
                                timeout : 3000
                            });
                            
                        }                   
                        $('#submit').text('Submit');
                        $('#submit').removeAttr("disabled");
                    },
                    dataType:'json'
                });
            }
        });
    }

	loadScript(BASE_URL+"js/plugin/jquery-validate/jquery.validate.min.js", function(){
		loadScript(BASE_URL+"js/plugin/jquery-form/jquery-form.min.js", validatefunction);
	});
	
</script>