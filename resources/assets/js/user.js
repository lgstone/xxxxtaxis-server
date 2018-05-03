

$().ready(function(){

});


$(".driverDetailBtn").click(function(){
    $.ajax({
        url : $(this).attr('href'),
        async : true,
        type : "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(data){
            $('#licenseNo').html(data.driving_license_number_);
            $('#licenseVersion').html(data.driving_license_version_);
            $('#licenseClass').html(data.driving_license_class_);            
            $('#licenseExpiry').html(data.driving_license_expires_);
            $('#frontPic').html('<img class="modalPic" src="'+data.driving_license_front_pic_+'" />');
            $('#backPic').html('<img class="modalPic" src="'+data.driving_license_back_pic_+'" />');

            $('#licenseModal').modal('show');
        },
        error : function() {
            alert("error");
        },
        dataType : "json"
    });
});

$(".vehicleDetailBtn").click(function(){
    $.ajax({
        url : $(this).attr('href'),
        async : true,
        type : "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(data){
            $('#vehiclePlate').html(data.plate_number_);
            $('#vehicleModel').html(data.brand_ +" " +data.model_+" "+data.year_);
            $('#vehiclePhoto').html('<img class="modalPic" src="'+data.vehicle_pic_+'" />');

            if(data.status_ == 0){
                $('#vehicleStatus').html('<font color="green">Ready</font>');            
            }else if(data.status_ == 1){
                $('#vehicleStatus').html('<font color="red">Broken</font>');   
            }else{
                $('#vehicleStatus').html('<font color="orange">Fixing</font>'); 
            }
            $('#vehicleModal').modal('show');
        },
        error : function() {
            alert("error");
        },
        dataType : "json"
    });
});


$(".vehicleDetailRegBtn").click(function(){
    $.ajax({
        url : $(this).attr('href'),
        async : true,
        type : "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(data){
            $('#vehiclePlate').html(data.vehicle_plate_number_);
            $('#vehicleModel').html(data.vehicle_brand_ +" " +data.vehicle_model_+" "+data.vehicle_year_);
            $('#vehicleStatus').html('<font color="green">Unknown</font>');
            $('#vehiclePhoto').html('<img class="modalPic" src="'+data.vehicle_pic_+'" />');
            $('#vehicleModal').modal('show');
        },
        error : function() {
            alert("error");
        },
        dataType : "json"
    });
});

$(".passRegBtn").click(function(){
    $('#confirmMsg').html('Are you sure to <font color="green">APPROVE</font> this driver apply?');
    $("#operateConfirm").val(1);
    $('#operateModal').modal('show');
});

$(".rejectRegBtn").click(function(){
    $('#confirmMsg').html('Are you sure to <font color="red">DECLINE</font> this driver apply?');
    $("#operateConfirm").val(0);
    $('#operateModal').modal('show');
});

$("#operateConfirm").click(function(){
    $token = $("meta[name='csrf-token']").attr('content');
    $op = $("#operateConfirm").val();
    if($op == 1){
        $url = $("#passRegBtn").attr('href');
    }else if($op == 0){
        $url = $("#passRegBtn").attr('href');
    }
    $.ajax({
        url : $url,
        async : true,
        type : "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType : "json",
        data : {'op' : $op},
        success : function(data){
            if (data.status == 0) {
                window.location.href = data.data.redirect;
            }
        }
    });  
});


$(".tripDetailBtn").click(function(){
    $token = $("meta[name='csrf-token']").attr('content');
    $trip_id = $("#trip_id").val();

    $.ajax({
        url : $(this).attr('href'),
        async : true,
        type : "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(data){
            $('#startTime').html(data.start_time_);
            $('#endTime').html(data.end_time_);
            $('#dep').html(data.departure_);
            $('#dest').html(data.destination_);
            $('#baseCharge').html(data.base_charge_);
            $('#pricePerKM').html(data.price_per_km_);
            $('#pricePerMin').html(data.price_per_min_);
            $('#priceMin').html(data.price_minimum_);
            $('#promotion').html(data.promotion_code_);

            $('#tripModal').modal('show');
        },
        error : function() {
            alert("error");
        },
        dataType : "json"
    });  
});

