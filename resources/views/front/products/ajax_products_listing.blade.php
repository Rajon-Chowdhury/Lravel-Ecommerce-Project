<?php use App\Product; ?>
		<div class="tab-pane  active" id="blockView">
			<ul class="thumbnails">
				@foreach($categoryProducts as $product)
					<li class="span3">
						<div class="thumbnail">
							<a href="{{ url('product/'.$product['id']) }}">
									@if(isset($product['main_image']))	
									<?php $product_image_path = 'images/product_images/small/'.$product['main_image'];?>
									@else
									 <?php $product_image_path = ''; ?>
									@endif		
								    @if(!empty($product['main_image'])&&file_exists($product_image_path))		
									  <img src="{{asset($product_image_path)}}" alt="">
								   
								    @else
								      <img src="{{asset('images/product_images/small/no-image.png')}}" alt="">
								    @endif  
							</a>
							<div class="caption">
								<h5>{{$product['product_name']}}&nbsp;{{$product['id']}}</h5>
								<p>
									{{$product['brand']['name']}}
								</p>
								<p>
									<?php $discounted_price = Product::getDiscountedPrice($product['id']);?>
								</p>
								<h4 style="text-align:center"><!-- <a class="btn" href="product_details.html"> <i class="icon-zoom-in"></i></a> --> <a class="btn" href="#">Add to <i class="icon-shopping-cart"></i></a> <a class="btn btn-primary" href="#">
						        @if($discounted_price>0)			
								   <del>Tk.{{$product['product_price']}}</del>
								    <font color="yellow">{{ $discounted_price }}</font>
								@else 
								   Tk.{{$product['product_price']}}
								@endif   
							    </a>
							   </h4>
								<p>
								 {{$product['fabric']}}	
								</p>
							    <p>
								 {{$product['sleeve']}}	
								</p>
							</div>
						</div>
					</li>
				@endforeach
			</ul>
			<hr class="soft"/>
		</div>
