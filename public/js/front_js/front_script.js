$(document).ready(function(){

    //Used for csrf token 
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });

    //Without using ajax update sort 
    // $("#sort").on('change',function(){ 	
    //  this.form.submit();

    // });

    $("#sort").on('change',function(){
         var fabric = get_filter('fabric');
         var sleeve = get_filter('sleeve');
         var pattern = get_filter('pattern');
         var fit = get_filter('fit');
         var occasion = get_filter('occasion');
         var sort = $(this).val();
         var url = $("#url").val();
         $.ajax({
           url:url,
           method:"post",
           data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
           success:function(data){
              $('.filter_products').html(data);
           }
         })
    });

    //For Fabric
    $(".fabric").on('click',function(){
         var fabric = get_filter('fabric');
         var sleeve = get_filter('sleeve');
         var pattern = get_filter('pattern');
         var fit = get_filter('fit');
         var occasion = get_filter('occasion');
         var sort = $("#sort option:selected").val();
         var url = $("#url").val();
         $.ajax({
           url:url,
           method:"post",
           data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
           success:function(data){
              $('.filter_products').html(data);
           }
         })
    });
    //For Sleeve
    $(".sleeve").on('click',function(){
         var fabric = get_filter('fabric');
         var sleeve = get_filter('sleeve');
         var pattern = get_filter('pattern');
         var fit = get_filter('fit');
         var occasion = get_filter('occasion');
         var sort = $("#sort option:selected").val();
         var url = $("#url").val();
         $.ajax({
           url:url,
           method:"post",
           data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
           success:function(data){
              $('.filter_products').html(data);
           }
         })
    }); 
    //For pattern
    $(".pattern").on('click',function(){
         var fabric = get_filter('fabric');
         var sleeve = get_filter('sleeve');
         var pattern = get_filter('pattern');
         var fit = get_filter('fit');
         var occasion = get_filter('occasion');
         var sort = $("#sort option:selected").val();
         var url = $("#url").val();
         $.ajax({
           url:url,
           method:"post",
           data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
           success:function(data){
              $('.filter_products').html(data);
           }
         })
    }); 
    //For fit
    $(".fit").on('click',function(){
         var fabric = get_filter('fabric');
         var sleeve = get_filter('sleeve');
         var pattern = get_filter('pattern');
         var fit = get_filter('fit');
         var occasion = get_filter('occasion');
         var sort = $("#sort option:selected").val();
         var url = $("#url").val();
         $.ajax({
           url:url,
           method:"post",
           data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
           success:function(data){
              $('.filter_products').html(data);
           }
         })
    }); 
    //For occasion
    $(".occasion").on('click',function(){
         var fabric = get_filter('fabric');
         var sleeve = get_filter('sleeve');
         var pattern = get_filter('pattern');
         var fit = get_filter('fit');
         var occasion = get_filter('occasion');
         var sort = $("#sort option:selected").val();
         var url = $("#url").val();
         $.ajax({
           url:url,
           method:"post",
           data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
           success:function(data){
              $('.filter_products').html(data);
           }
         })
    });     
    function get_filter(class_name){
        var filter = [];
        $('.'+class_name+':checked').each(function(){
            filter.push($(this).val());
        });
        return filter;
    }

    //Changeing Price based on size 
    $("#getPrice").on('change',function(){
        var size = $(this).val();
        if(size==""){
            alert("Please select Size");
            return false;
        }
        var product_id = $(this).attr("product-id");

        $.ajax({
           url:'/get-product-price',
           type:"post",
           data:{size:size,product_id:product_id},
           success:function(resp){
              //alert(resp);
              $('.getAttrPrice').html("Tk."+resp);
           },error:function(){
            alert("Error");
           }
        });
        
    });
 

});