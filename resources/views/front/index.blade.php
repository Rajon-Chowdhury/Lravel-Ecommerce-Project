<?php use App\Product; ?>
 @extends('layouts.front_layout.front_layout')
 @section('content')
   <div class="span9">
	<div class="well well-small">
		<h4>Featured Products <small class="pull-right">{{$featuredItemsCount}} featured products</small></h4>
		<div class="row-fluid">
			<div id="featured" @if($featuredItemsCount>4) class="carousel slide" @endif>
				<div class="carousel-inner">
					@foreach($featuredItemsChunk as $key => $featuredItem)
					<div class="item @if($key==1) active @endif">
						<ul class="thumbnails">
							@foreach($featuredItem as $item)
							<li class="span4">
								<div class="thumbnail">
								  <i class="tag"></i>
								  <a href="{{ url('product/'.$item['id']) }}">
									<?php $product_image_path = 'images/product_images/small/'.$item['main_image'];?>	
								    @if(!empty($item['main_image'])&&file_exists($product_image_path))		
									  <img src="{{asset($product_image_path)}}" alt="">
								   
								    @else
								      <img src="{{asset('images/product_images/small/no-image.png')}}" alt="">
								    @endif  
                                   </a>
									<div class="caption">
										<h5>{{$item['product_name']}}</h5>
										<?php $discounted_price = Product::getDiscountedPrice($item['id']);?>
										<h4><a class="btn" href="{{ url('product/'.$item['id']) }}">VIEW</a> 
											<span class="pull-right" style="font-size: 15px;">
												@if($discounted_price>0)
												<del>Tk.{{$item['product_price']}}</del><font color="red"> Tk.{{ $discounted_price }}</font>
												@else
												 Tk.{{$item['product_price']}}
												@endif 
											</span>
										</h4>
									
									</div>
								</div>
							</li>
							@endforeach
						</ul>
					</div>
					@endforeach
				</div>
<!-- 				<a class="left carousel-control" href="#featured" data-slide="prev">‹</a>
				<a class="right carousel-control" href="#featured" data-slide="next">›</a> -->
			</div>
		</div>
	</div>
	<h4>Latest Products </h4>
	<ul class="thumbnails">
	   @foreach($newProducts as $product)	
		<li class="span3">
			<div class="thumbnail">
				<a  href="{{ url('product/'.$product['id']) }}">
					<?php $product_image_path = 'images/product_images/small/'.$product['main_image'];?>	
					    @if(!empty($product['main_image'])&&file_exists($product_image_path))		
						  <img style="width: 160px;" src="{{asset($product_image_path)}}" alt="">
					   
					    @else
					      <img style="width: 160px;" src="{{asset('images/product_images/small/no-image.png')}}" alt="">
					    @endif 
				</a>
				<div class="caption">
					<h5>{{$product['product_name']}}</h5>
					<p>
						{{$product['product_code']}}
					</p>
					<?php $discounted_price = Product::getDiscountedPrice($product['id']);?>
					<h4 style="text-align:center"><!-- <a class="btn" href="{{ url('product/'.$product['id']) }}"> <i class="icon-zoom-in"></i></a> --> <a class="btn" href="#">Add to <i class="icon-shopping-cart"></i></a> <a class="btn btn-primary" href="#"> 
								<span class="pull-right" style="font-size: 15px;">
									@if($discounted_price>0)
									<del>Tk.{{$product['product_price']}}</del><font color="yellow"> Tk.{{ $discounted_price }}</font>
									@else
									 Tk.{{$product['product_price']}}
									@endif 
								</span>
					</a>
				</h4>
				</div>
			</div>
		</li>
	   @endforeach	
	</ul>
 </div>
@endsection