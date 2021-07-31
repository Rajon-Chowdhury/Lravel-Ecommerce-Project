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
              if(resp['discount']>0){
                $('.getAttrPrice').html("<del>Tk."+resp['product_price']+"</del> Tk."+resp['final_price']);   
              }else{
                $('.getAttrPrice').html("Tk."+resp['product_price']);
              }
           },error:function(){
            alert("Error");
           }
        });
        
    });

    //Update Item Cart

    $(document).on('click','.btnItemUpdate',function(){
          if($(this).hasClass('qtyMinus')){
             var quantity = $(this).prev().val();
             if(quantity<=1){
              alert("Item quantity must be 1 or grater");
              return false;
              }else{
                 new_qty = parseInt(quantity)-1;
              }
          }
          if($(this).hasClass('qtyPlus')){
             var quantity = $(this).prev().prev().val();
             
             new_qty = parseInt(quantity)+1;
            
          }
          var cartid = $(this).data('cartid');
          $.ajax({
            data:{"cartid":cartid,"qty":new_qty},
            url:'/update-cart-item-qty',
            type:'post',
            success:function(resp){
                if(resp.status==false){
                    alert(resp.message);
                }
                $("#AppendCartItems").html(resp.view); 
            },error:function(){
                alert("Error");
            }
          });
    }); 

    //Delete Item Cart

    $(document).on('click','.btnItemDelete',function(){
          var cartid = $(this).data('cartid');
          var result = confirm("Want to delete This Cart Item")
          if(result){
          $.ajax({
            data:{"cartid":cartid},
            url:'/delete-cart-item',
            type:'post',
            success:function(resp){
                $("#AppendCartItems").html(resp.view); 
            },error:function(){
                alert("Error");
            }
          });
      }
    }); 

    // validate register form on keyup and submit
    $("#registerForm").validate({
        rules: {
             name: "required",
             mobile: {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits:true
            },
            email: {
                required: true,
                email: true,
                remote:"check-email"
            },
            password: {
                required: true,
                minlength: 6
            },
        },
        messages: {
            name: "Please enter your name",
            mobile: {
                required: "Please enter your moubile",
                minlength: "Your mobile must consist of 10 digits",
                maxlength: "Your mobile must consist of 10 digits",
                digits:"Please enter your valid Mobile"
            },
            email: {
                required: "Please provide your eamil",
                email: "Please enter your valid email",
                remote:"Email already exists"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            }
        }
    });
    // validate login form on keyup and submit
    $("#loginForm").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 6
            },
        },
        messages: {
            email: {
                required: "Please provide your eamil",
                email: "Please enter your valid email",
                remote:"Email already exists"
            },
            password: {
                required: "Please Enter your password",
                minlength: "Your password must be at least 6 characters long"
            }
        }
    });
 

});