<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Cinema Paradiso</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="{{ asset('css/stylesheet.css') }}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('imagens/cppar2.ico') }}" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

</head>
<body>

	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span href="#" class="sr-only">MoviGeek</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{ route('store') }}"><span class="hideable glyphicon glyphicon-film""></span> CinemaParadiso</a>
			</div>


			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					@if($session_id)
					<li><a href="{{ route('logout') }}"><span class="hideable glyphicon glyphicon-log-out" ></span> Logout</a></li>
					@endif
					@if(!$session_id)
					<li><a href="{{ route('store/register') }}"><span class="hideable glyphicon glyphicon-registration-mark"></span> Register</a></li>
					@endif
					@if(!$session_id)
					<li><a href="{{ route('store/login') }}"><span class="hideable glyphicon glyphicon-log-in" ></span> Login</a></li>
					@endif
					@if($session_id)
					<li><a href="{{ route('orders') }}"><span class="hideable glyphicon glyphicon-tags" ></span> My orders</a></li>
					@endif
					<li class="hideable"><a class="fa fa-shopping-cart" href="{{ route('checkout') }}"> Shopping cart: {{$items_in_cart}} Items - {{$total}} €</a></li>
					@if($session_id)
					<li><a href="{{ route('orders') }}"><span class="hideable glyphicon glyphicon-user" ></span> Logged in as: {{$session_name}}</a></li>
					@else
					<li><a href="#"><span class="hideable glyphicon glyphicon-sunglasses" ></span> Not logged in</a></li>
					@endif

					<li id="nav-more" class="dropdown hidden"><!-- the 'more' dropdown item, originally hidden -->
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">MORE <span class="caret"></span></a>
						<ul class="dropdown-menu">
						</ul>
					</li>

				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
		<!--/.container-fluid -->
	</nav>


	<main class="container">
		<div class="row">
			<div class="sidebar text-center">
				<img src="{{ asset('imagens/cppar2.png') }}" class="sidebarimg">
				<h3>Happy Holidays</h3>
				<p></p>
				<a href="{{ route('store') }}"><button  class="btn btn-secondary">All
				</button>
				<p></p>
				@foreach ($categories as $category)
				<a href="{{ route('categoryid', [$category->id]) }}"><button  class="btn btn-secondary">
					{{$category->name}}
				</button>
				<p></p>
				@endforeach  
			</div>
			<div class="content">
				<div class="container text-center">
					<div id="myCarousel" class="carousel slide" data-ride="carousel" style="max-width: 800px; margin: 0 auto">
						<!-- Indicators -->
						<ol class="carousel-indicators">
							<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
							<li data-target="#myCarousel" data-slide-to="1"></li>
						</ol>

						<!-- Wrapper for slides -->
						<div class="carousel-inner">
							<div class="item active">
								<img src="{{ asset('imagens/gotg.PNG') }}" alt="gotg" style="width:100%; height: 100px">
							</div>

							<div class="item">
								<img src="{{ asset('imagens/gotg2.PNG') }}"  alt="loule" style="width:100%; height: 100px">
							</div>
						</div>

						<!-- Left and right controls -->
						<a class="left carousel-control" href="#myCarousel" data-slide="prev">
							<span class="glyphicon glyphicon-chevron-left"></span>
							<span class="sr-only">Próximo</span>
						</a>
						<a class="right carousel-control" href="#myCarousel" data-slide="next">
							<span class="glyphicon glyphicon-chevron-right"></span>
							<span class="sr-only">Anterior</span>
						</a>
					</div>
				</div>
				<p></p>
				<div class="container" style="margin: 0 auto">		
					<div class="row" >
						@foreach ($products as $product)
						<div class="col-md-3 col-sm-3">
							<div class="product-grid2">
								<div class="product-image2 gfg imageContainer shopimg">
									<a href="#">
										<img class="pic-1" src="{{ asset('imagens') }}/{{$product->image}}">
										<img class="pic-2" src="{{ asset('imagens') }}/{{$product->catimage}}">
									</a>
									
									<a class="add-to-cart fa fa-shopping-cart" name="addtocart" href="{{ route('addtocartid', [$product->id]) }}" >Add to cart</a>
								</div>
								<div class="product-content">
									<button type="button" class="btn btn-info" data-container="body" data-toggle="popover" data-placement="top" data-content="{{$product->description}}.">
										+Show Description
									</button>
									<p></p>
									<h3 class="title"><a href="#">{{$product->name}}</a></h3>
									<p>{{$product->catname}}</p>	
									<span class="price">{{$product->price}}€</span>
								</div>

							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</main>		
	<script>
		$(function () {
			$('[data-toggle="popover"]').popover()
		})
	</script>
	<footer class="container-fluid text-center">
		<p>Powered by Diogo Martins</p>
	</footer>

</body>
</html>
