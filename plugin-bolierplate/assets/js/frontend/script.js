$jQu                        = jQuery;
var selected_city           ="";
var selected_communities    ="";
var selected_categories     ="";
var selected_schools        ="";
var no_community_message    ="There is no Community.";
//var max_image_size          ="1024000";
var modals					= [];
/*Date picker script for date class*/
$jQu( function() {
    $jQu( ".date" ).datepicker({
        dateFormat: "mm/dd/yy",
        maxDate: '0'
    });
} );
/* alert Message script */
function warningMessage(message)
{
    $jQu('#alertMsg').removeClass('alert-warning').removeClass('alert-success');
    $jQu('#alertMsg').addClass("alert-warning").html(message);
    $jQu('html, body').animate({
        scrollTop: $jQu("form").offset().top -100
    }, 2000);
}
function successMessage(message)
{
    $jQu('#alertMsg').removeClass('alert-warning').removeClass('alert-success');
    $jQu('#alertMsg').addClass("alert-success").html(message);
    $jQu('html, body').animate({
        scrollTop: $jQu("form").offset().top -100
    }, 2000);
}
function warningSpecificMessage(id,message)
{
    $jQu("#"+id).removeClass('alert-warning').removeClass('alert-success');
    $jQu('#'+id).addClass("alert-warning").html(message);
}
function successSpecificMessage(id,message)
{
    $jQu("#"+id).removeClass('alert-warning').removeClass('alert-success');
    $jQu("#"+id).addClass("alert-success").html(message);

}
function modalWarningMessage(modal,message)
{

    modal.find('.alert').removeClass('alert-warning').removeClass('alert-success');
    modal.find('.alert').addClass("alert-warning").html(message);
    
}
function modalSuccessMessage(modal,message)
{
    modal.find('.alert').removeClass('alert-warning').removeClass('alert-success');
    modal.find('.alert').addClass("alert-success").html(message);
    modal.find('form')[0].reset();
   // var timer = modal.data('timer') ? modal.data('timer') : 7000;
    var timer = 800;
    modal.delay(timer).fadeOut(200, function () {
		modal.find('.alert').removeClass('alert-warning').removeClass('alert-success').html("");
        modal.modal('hide');
        if($jQu('.modal:visible').length>0){
			setTimeout(function(){ $jQu("body").addClass("modal-open");}, 500);
	    }
    });
}
/* Check username/email exists in user table */
$jQu(document).on('blur', '#email', function(e) {
    var email=$jQu(this).val();
    var disabled =$jQu(this).prop('disabled');
    if(email!="" && !disabled) {
        $jQu.ajax({
            method: "POST",
            url: adminAjax,
            data: {action: "lmapp_ajax", task: "check_email", security: window.lmapps_site_nonce, email: email},
            crossDomain: true,
            dataType: 'json'
        }).done(function (response) {
            var res = eval(response);
            if (res.success != "undefined" && res.success == 1) {
                successSpecificMessage("alertEmailMsg",res.message);
            }
            else {
                warningSpecificMessage("alertEmailMsg",res.message);
            }
        }).fail(function (response) {
            var res = eval(response.responseJSON);
            if (res.success != "undefined" && res.success == 0) {
                warningMessage(res.message);
            }
        });
    }
});
/* Check username/email exists in user table */
$jQu(document).on('blur', '#username', function(e) {
    var username=$jQu(this).val();
    var disabled =$jQu(this).prop('disabled');
    if(username!="" && !disabled) {
        $jQu.ajax({
            method: "POST",
            url: adminAjax,
            data: {
                action: "lmapp_ajax",
                task: "check_username",
                security: window.lmapps_site_nonce,
                username: username
            },
            crossDomain: true,
            dataType: 'json'
        }).done(function (response) {
            var res = eval(response);
            if (res.success != "undefined" && res.success == 1) {
                successSpecificMessage("alertUserNameMsg",res.message);
            }
            else {
                warningSpecificMessage("alertUserNameMsg",res.message);
            }
        }).fail(function (response) {
            var res = eval(response.responseJSON);
            if (res.success != "undefined" && res.success == 0) {
                warningMessage(res.message);
            }
        });
    }
});
/* State Modal Script */
$jQu(document).on('click', '.state-modal', function(e) {
    e.preventDefault();
    modals["state-modal"] = $jQu('#myModal');
    modals["state-modal"].modal('show').find('.custom-modal-body').load($jQu(this).attr('href'));
    $jQu('#myModal').find('.modal-title').html('Add State');
    selected_city=$jQu('#city_id').val();
});

/* Save State From Modal */
$jQu(document).on('submit', '.modal #formState', function(event) {
    event.preventDefault();
    var modal=$jQu('#myModal');
    var name=modal.find('#name').val();
    $jQu.ajax({
        method: "POST",
        url: adminAjax,
        data: {action: "lmapp_ajax",task: "save_state",security: window.lmapps_site_nonce, name: name,selected_city:selected_city,modal:true},
        crossDomain: true,
        dataType: 'json'
    }).done(function (response) {
        var res = eval(response);
        if (res.success != "undefined" && res.success == 1) {
            if(res.stateList !="undefined") {
                $jQu('.state_list').html(res.stateList);
                $jQu("form").validator('update');
                $jQu('#city_id').find('option:not(:first)').remove();
                $jQu('#city_id').trigger('change');
            }
            modalSuccessMessage(modal,res.message);
        }
        else
        {
            modalWarningMessage(modal,res.message);
        }
    }).fail(function (response) {
        var res = eval(response.responseJSON);
        if (res.success != "undefined" && res.success == 0) {
            modalWarningMessage(modal,res.message);
        }
    });
});
/* Get City according to state */
$jQu(document).on('change', '#state_id', function(event) {
    var state_id=$jQu(this).val();
    if(state_id!="") {
        $jQu.ajax({
            method: "POST",
            url: adminAjax,
            data: {
                action: "lmapp_ajax",
                task: "get_cities_by_state",
                security: window.lmapps_site_nonce,
                state_id: state_id
            },
            crossDomain: true,
            dataType: 'json'
        }).done(function (response) {
            var res = eval(response);
            if (res.success != "undefined" && res.success == 1) {
                $jQu('.city_list').html(res.cityList);
                $jQu("form").validator('update');
                $jQu('#city_id').trigger('change');
            }
            else {
                warningMessage(res.message);
            }
        }).fail(function (response) {
            var res = eval(response.responseJSON);
            if (res.success != "undefined" && res.success == 0) {
                warningMessage(res.message);
            }
        });
    }
    else
    {
        $jQu('#city_id').find('option:not(:first)').remove();
        $jQu('#city_id').trigger('change');
    }
});
/* City Modal Script */
$jQu(document).on('click', '.city-modal', function(e) {
    e.preventDefault();
    if($jQu('#state_id').val()=="" || $jQu('#state_id').val()==undefined)
    {
        alert("Firstly please select state.");
    }
    else {
        var state_id=$jQu('#state_id').val();
        var state_name=$jQu('#state_id').find("option:selected").text();
         modals["city-modal"] = $jQu('#myModal');
        $jQu('#myModal').modal('show').find('.custom-modal-body').load($jQu(this).attr('href'),
            function(response, status, xhr) {
                $jQu('#myModal').find('#state_name').html(state_name);
                $jQu('#myModal').find('#state_id').val(state_id);
            });
        $jQu('#myModal').find('.modal-title').html('Add City');
    }
});
/* Save City From Modal */
$jQu(document).on('submit', '.modal #formCity', function(event) {
    event.preventDefault();
    var modal=$jQu('#myModal');
    var state_id=modal.find('#state_id').val();
    var name=modal.find('#name').val();
    var community_type=$jQu('.community_list').attr('data');
    $jQu.ajax({
        method: "POST",
        url: adminAjax,
        data: {action: "lmapp_ajax",task: "save_city",security: window.lmapps_site_nonce, state_id:state_id, name:name,modal:true},
        crossDomain: true,
        dataType: 'json'
    }).done(function (response) {
        var res = eval(response);
        if (res.success != "undefined" && res.success == 1) {
            if(res.cityList !="undefined") {
                $jQu('.city_list').html(res.cityList);
                $jQu("form").validator('update');
                $jQu('#city_id').trigger('change');
            }
            modalSuccessMessage(modal,res.message);
        }
        else
        {
            modalWarningMessage(modal,res.message);
        }
    }).fail(function (response) {
        var res = eval(response.responseJSON);
        if (res.success != "undefined" && res.success == 0) {
            modalWarningMessage(modal,res.message);
        }
    });
});
/* Get Communities by city */
$jQu(document).on('change', '#city_id', function(event) {
    event.preventDefault();
    $jQu("form").validator('update');
});
/* Community Modal Script */
$jQu(document).on('click', '.community-modal', function(e) {
    e.preventDefault();
    if($jQu('#city_id').val()==undefined || $jQu('#city_id').val()=="")
    {
        alert("Firstly please select City.");
    }
    else {
        var city_id=$jQu('#city_id').val();
        var city_name=$jQu('#city_id').find("option:selected").text();
        var community_type=$jQu('.community_list').attr('data');
        var communities = [];
         modals["community-modal"] = $jQu('#myModal');
        selected_community="";
        $jQu("input[name^='community_id']:checked").each(function(){
            communities.push($jQu(this).val());
        });
        selected_communities=communities.join(',');
        $jQu('#myModal').modal('show').find('.custom-modal-body').load($jQu(this).attr('href'),
            function(response, status, xhr) {
                $jQu('#myModal').find('#city_name').html(city_name);
                $jQu('#myModal').find('#city_id').val(city_id);
                $jQu('#myModal').find('#type').val(community_type);
            });
        $jQu('#myModal').find('.modal-title').html('Add Community');
    }
});
/* Color picker code */
 $jQu(function(){
  var colpick =  $jQu('.colorpicker').each( function() {
    $jQu(this).minicolors({
      control: $jQu(this).attr('data-control') || 'hue',
      inline: $jQu(this).attr('data-inline') === 'true',
      letterCase: 'lowercase',
      opacity: false,
      change: function(hex, opacity) {
        if(!hex) return;
        if(opacity) hex += ', ' + opacity;
        try {
          console.log(hex);
        } catch(e) {}
         $jQu(this).select();
      },
      theme: 'bootstrap'
    });
  });
  
  var $inlinehex =  $jQu('#inlinecolorhex h3 small');
   $jQu('#inlinecolors').minicolors({
    inline: true,
    theme: 'bootstrap',
    change: function(hex) {
      if(!hex) return;
       $jQuinlinehex.html(hex);
    }
  });
});
/* Sport Modal Script */
$jQu(document).on('click', '.sport-modal', function(e) {
    e.preventDefault();
    modals["sport-modal"] = $jQu('#myModal');
    $jQu('#myModal').modal('show').find('.custom-modal-body').load($jQu(this).attr('href'));
    $jQu('#myModal').find('.modal-title').html('Add Sport');

});
/* Save Sport From Modal */
$jQu(document).on('submit', '.modal #formSport', function(event) {
    event.preventDefault();
    var modal=$jQu('#myModal');
    var name=modal.find('#name').val();
    $jQu.ajax({
        method: "POST",
        url: adminAjax,
        data: {action: "lmapp_ajax",task: "save_sport",security: window.lmapps_site_nonce, name: name,selected_city:selected_city,modal:true},
        crossDomain: true,
        dataType: 'json'
    }).done(function (response) {
        var res = eval(response);
        if (res.success != "undefined" && res.success == 1) {
            if(res.stateList !="undefined") {
                $jQu('.sport_list').html(res.stateList);
                $jQu("form").validator('update');
            }
            modalSuccessMessage(modal,res.message);
        }
        else
        {
            modalWarningMessage(modal,res.message);
        }
    }).fail(function (response) {
        var res = eval(response.responseJSON);
        if (res.success != "undefined" && res.success == 0) {
            modalWarningMessage(modal,res.message);
        }
    });
});
/* Close model script */
$jQu(document).on('click', '.modal-footer button[type="button"]', function (event) {

	if($jQu(this).parents("#formState").length > 0){
		modals["state-modal"].modal("hide");
		if($jQu('.modal:visible').length>1){
			setTimeout(function(){ $jQu("body").addClass("modal-open");}, 500);
	    }
	}
	if($jQu(this).parents("#formCity").length > 0){
		modals["city-modal"].modal("hide");
		if($jQu('.modal:visible').length>1){
			setTimeout(function(){ $jQu("body").addClass("modal-open");}, 500);
	    }
	}
	if($jQu(this).parents("#formSport").length > 0){
		modals["sport-modal"].modal("hide");
		if($jQu('.modal:visible').length>1){
			setTimeout(function(){ $jQu("body").addClass("modal-open");}, 500);
	    }
	}
	if($jQu(this).parents("#formCommunity").length > 0){
		modals["community-modal"].modal("hide");
		if($jQu('.modal:visible').length>1){
			setTimeout(function(){ $jQu("body").addClass("modal-open");}, 500);
	    }
	}
	event.preventDefault();
	return false;
});
/* Close model script */
$jQu(document).on('click', 'div.school#myModal', function (event) {
	    if($jQu('.modal:visible').length>1){
			setTimeout(function(){ $jQu("body").addClass("modal-open");}, 500);
	    }
		//event.preventDefault();
	  //  return false;
});
 $jQu(document).on('change', '#imgInp', function(e) {   
        readURL(this);
    });

/* Change Password */
$jQu('#formChangePassword').validator().on('submit', function (e) {
    var form=$jQu(this);
    e.preventDefault();
    if (!e.isDefaultPrevented()) {
        $jQu('.loader').css("display","inline-block");
        var dataArr = $jQu(this).serializeArray();
        dataArr.push({ name: "action", value:"lmapp_ajax"});
        dataArr.push({ name: "task", value:"change_password"});
        dataArr.push({ name: "security", value:window.lmapps_site_nonce});
        $jQu.ajax({
            method: "POST",
            url: adminAjax,
            data: dataArr,
            crossDomain: true,
            dataType: 'json'
        }).done(function (response) {
            $jQu('.loader').hide();
            var res = eval(response);
            if (res.success != "undefined" && res.success == 1) {
                successMessage(res.message);
                form[0].reset();
            }
            else {
                warningMessage(res.message);
            }
        }).fail(function (response) {
            var res = eval(response.responseJSON);
            if (res.success != "undefined" && res.success == 0) {
                warningMessage(res.message);
                $jQu('.loader').hide();
            }
        });
    }
});
/* Category Modal Script */
$jQu(document).on('click', '.category-modal', function(e) {
    e.preventDefault();
    var categories = [];
    modals["category-modal"] = $jQu('#myModal');
    $jQu("input[name^='category_id']:checked").each(function(){
        categories.push($jQu(this).val());
    });
    selected_categories=categories.join(',');
    $jQu('#myModal').modal('show').find('.custom-modal-body').load($jQu(this).attr('href'),
        function(response, status, xhr) {

        });
    $jQu('#myModal').find('.modal-title').html('Add Category');

});
