<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie; 
use App\Store_model;
use Session;
use Mail;

class Store extends Controller
{
	public function index($id = FALSE)
	{
		if(Cookie::has('siteAuth')){
			$remember_digest = Cookie::get('siteAuth');
			$validate = Store_model::check_remember_digest($remember_digest);
			session(['id' => $validate->id]);
			session(['name' => $validate->name]);
			session(['email' => $validate->email]);
		}	
		
		if($id){
			$productcat = Store_model::get_products($id);
			if($productcat){
				$data['products'] = $productcat;
			}
			else{
				$data['products'] = Store_model::get_all_products();
			}
		}
		else{
			$data['products'] = Store_model::get_all_products();
		}

		if(session()->has('id'))
		{
			$data['session_name'] = Session::get('name');
			$data['session_email'] = Session::get('email');
			$data['session_id'] = Session::get('id');
		}
		else
		{

			$data['session_name'] = 'Not logged In';
			$data['session_email'] = '';
			$data['session_id'] = '';
		}

		if(session()->has('cart'))
		{
			$cart = Session::get('cart');
			$data['items_in_cart'] = Session::get('items_in_cart');
			$data['total'] = Session::get('total');
		}
		else{
			$data['items_in_cart'] = '0';
			$data['total'] = '0';
		}

		$data['categories'] = Store_model::get_categories();		
		return view('Products', $data);
	}

	public function register()
	{
		if(session()->has('id'))
		{		
			session(['MESSAGE' => 'You must sign out in order to create a different account! ']);
			session(['thegif' => 'error2']);
			return redirect('store/message');	
		}
		else{
			return view('register');
		}
	} 

	public function register_action()
	{
		$this->validate(request(), [
			'name'=>'required|min:2',
			'email'=>'required|unique:customers,email',
			'password'=>'required|min:5',
			'passwordconfirmation'=>'required|same:password'
		]);

		$name=request('name');
		$email=request('email');
		$password=request('password');
		$passwordconfirmation=request('passwordconfirmation');
		Store_model::register_user($name,$email,$password,$passwordconfirmation);
		$data['title'] = "Store";
		session(['MESSAGE' => 'Success, you have been registered in this Website. Log in to start Shopping!']);
		session(['thegif' => 'dicaprio']);
		return redirect('store/message');
	}

	public function login()
	{
		if(session()->has('id'))
		{
			session(['MESSAGE' => 'You must sign out in order to login in a different account! ']);
			session(['thegif' => 'error']);
			return redirect('store/message');
		}
		else{
			$data['MESSAGE'] = '';
			return view('login', $data);
		}
	} 

	public function login_action()
	{
		$this->validate(request(), [
			'email'=>'required',
			'password'=>'required',
			'autologin'=>'in:0,1'

		]);

		$email=request('email');
		$password=request('password');

		$results= Store_model::login($email,$password);

		if(request('autologin')==1)
		{
			Cookie::queue(Cookie::forget('siteAuth'));
			$cookie_name = 'siteAuth';
          $cookie_time = (60 * 24 * 30); // 30 days
          $remember_digest = substr(md5(time()),0,32);
          Cookie::queue($cookie_name,  $remember_digest, $cookie_time);
          Store_model::set_remember_digest($email, $remember_digest); 
      }
      else{
          Cookie::queue(Cookie::forget('siteAuth')); //mexi no session.php para poder testar esta funcionalidade no meu browser
      }

      if ($results==false)
      {
      	$data['MESSAGE'] = 'Login failed';
      	return view('login', $data);
      }
      else
      { 
      	session(['MESSAGE' => ' Welcome back!']);
      	session(['thegif' => 'welcome']);
      	session(['id' => $results->id]);
      	session(['name' => $results->name]);
      	session(['email' => $email]);
      	$custid = Session::get('id');
      	if(session()->has('cart')){
      		return redirect('store/message');
      	}
      	else {
      		if(Cookie::has($custid)){
      			$cart = Cookie::get($custid);
      			Session::put('cart', $cart);
      			$carts = Session::get('cart');
      			$total = Session::get('total');
      			$items_in_cart = Session::get('items_in_cart');

      			foreach($carts as $cartproduct){
      				$total += $cartproduct['prodsprice'];
      				$items_in_cart += $cartproduct['quantity'];
      			}

      			Session::put('total', $total);
      			Session::put('items_in_cart', $items_in_cart);

      		}
      		else{
      			return redirect('store/message');
      		}
      	}
      	return redirect('store/message');
      }

  } 

  public function message()
  {
  	if(session()->has('id'))
  	{       	
  		$data['session_id'] = Session::get('id');
  	}else{
  		$data['session_id'] = '';
  	}

  	$data['MESSAGE'] = Session::get('MESSAGE');
  	$data['thegif'] = Session::get('thegif');
  	return view('message', $data);
  }

  function logout()
  {
  	if(session()->has('id'))
  	{ 
  		$custid = Session::get('id');
  		Cookie::queue(Cookie::forget($custid));
	     $cookie_time = (60 * 24 * 30); // 30 days
	     $cart = Session::get('cart');
	     Cookie::queue($custid, $cart, $cookie_time);
	     session()->forget('name');
	     session()->forget('id');
	     session()->forget('email');
	     session()->forget('custid');
	     session()->forget('cart');
	     session()->forget('total');
	     session()->forget('items_in_cart');
	     Cookie::queue('siteAuth', '');
	     Cookie::queue(Cookie::forget('siteAuth'));
	     $data['session_id'] = '';
	     $data['MESSAGE'] = 'See you back soon';
	     $data['thegif'] = 'leave';
	     return view('message', $data);
	 }
	 else{
	 	return redirect()->route( 'store');
	 }
	}

	public function cartItemInsert($id = FALSE)  
	{

		$cart = Session::get('cart');
		$product_id = $id;
		$product = Store_model::get_product($product_id);

		if($product){
			if(isset($cart[$product_id])){
				$cart[$product_id]['quantity'] += 1;
				$cart[$product_id]['prodsprice'] += $product->price;
			}
			else{
				$cart[$product_id] = array(
					"id" => $product->id,
					"name" => $product->name,
					"categorie" => $product->catname,
					"image" => $product->image,
					"quantity" => 1,
					"price" => $product->price,
					"prodsprice" =>$product->price
				);
			}

			Session::put('cart', $cart);
			$carts = Session::get('cart');

			Session::forget('total');
			Session::forget('items_in_cart');
			$total = Session::get('total');
			$items_in_cart = Session::get('items_in_cart');

			foreach($carts as $cartproduct){
				$total += $cartproduct['prodsprice'];
				$items_in_cart += $cartproduct['quantity'];
			}

			Session::put('total', $total);
			Session::put('items_in_cart', $items_in_cart);

			if(session()->has('id'))
		{
		 $custid = Session::get('id');
  		 Cookie::queue(Cookie::forget($custid));
	     $cookie_time = (60 * 24 * 30); // 30 days
	     $cart = Session::get('cart');
	     Cookie::queue($custid, $cart, $cookie_time);
	 	}
			
			return redirect()->route( 'store');
		}
		else{
			return redirect()->route( 'store');
		}
	}

	public function checkout()
	{
		$data['products'] = Session::get('cart');
		$data['total'] = Session::get('total');

		if(session()->has('id'))
		{
			$data['INOUT'] = 'Logout';
			$data['session_name'] = Session::get('name');
			$data['session_email'] = Session::get('email');
			$data['session_id'] = Session::get('id');
		}
		else
		{
			$data['INOUT'] = '';
			$data['session_name'] = 'Not logged In';
			$data['session_email'] = '';
			$data['session_id'] = '';
		}
		if($data['products'] == 0){
			return redirect()->route( 'store');
		}
		return view('checkout', $data);
	}

	public function checkoutAction()
	{
		if(session()->has('id'))
		{
			$customer_id = Session::get('id');
			$total = Session::get('total');
			if(session()->has('cart'))
			{
				$create = Store_model::create_order($customer_id, $total);
				if($create){
					$ordercheck = Store_model::checkorder($customer_id, 1);
					$order_id = $ordercheck->id;

					$cart = Session::get('cart');
					foreach ($cart as $key => $value) {
						if($value['quantity'] > 0){
							Store_model::insert_order_item($order_id, $value['id'], $value['quantity']);
						}
					}

					$email = Session::get('email');

					session()->forget('cart');
					session()->forget('items_in_cart');
					session()->forget('total');
					session(['MESSAGE' => 'Thanks for buying our products! Your order is now in process']);
					session(['thegif' => 'blingbling']);
					return redirect()->route( 'store/message');
				}
				else{
					return redirect()->route( 'checkout');
				}
			}else{
				return redirect()->route( 'checkout');
			}
		}	
		else{
			return redirect()->route( 'store/login');
		}
	}

	public function checkoutIncrease($id = FALSE)  
	{

		$cart = Session::get('cart');
		$product_id = $id;
		$product = Store_model::get_product($product_id);
		if(isset($cart[$product_id])){
			$cart[$product_id]['quantity'] += 1;
			$cart[$product_id]['prodsprice'] += $product->price;
			Session::put('cart', $cart);
			$items_in_cart = Session::get('items_in_cart');
			$items_in_cart+=1;
			Session::put('items_in_cart', $items_in_cart);

			$total = Session::get('total');
			$total += $product->price;
			if($total==0){
				session()->forget('cart');
			}
			Session::put('total', $total);
			return redirect()->route( 'checkout');
		}
		else{
			return redirect()->route( 'checkout');
		}

	}

	public function checkoutDecrease($coisoid = FALSE)  
	{
		$cart = Session::get('cart');
		$product_id = $coisoid;
		$product = Store_model::get_product($product_id);
		if(isset($cart[$product_id])){
		if($cart[$product_id]['quantity'] > 0){
			$cart[$product_id]['quantity'] -= 1;
			$cart[$product_id]['prodsprice'] -= $product->price;
			Session::put('cart', $cart);
			$items_in_cart = Session::get('items_in_cart');
			$items_in_cart-=1;
			Session::put('items_in_cart', $items_in_cart);

			$total = Session::get('total');
			$total -= $product->price;
			if(!$total>0){
				session()->forget('cart');
			}
			Session::put('total', $total);
		}
		return redirect()->route( 'checkout');
	}
	else{
		return redirect()->route( 'checkout');
	}

	}

	public function checkoutRemove($id= FALSE)  
	{
		$cart = Session::get('cart');
		$product_id = $id;
		$product = Store_model::get_product($product_id);
		if(isset($cart[$product_id])){
		$items_to_subtract = $cart[$product_id]['quantity'];
		$subtract_from_total = $cart[$product_id]['prodsprice'];
		unset($cart[$product_id]);
		Session::put('cart', $cart);
		$items_in_cart = Session::get('items_in_cart');
		$items_in_cart = $items_in_cart - $items_to_subtract;
		Session::put('items_in_cart', $items_in_cart);

		$total = Session::get('total');
		$total = $total - $subtract_from_total;
		if($total==0){
			session()->forget('cart');
		}
		Session::put('total', $total);
		return redirect()->route( 'checkout');
	}
	else{
		return redirect()->route( 'checkout');
	}

	}

	public function orders()  
	{
		$data['products'] = Session::get('cart');
		$data['total'] = Session::get('total');

		if(session()->has('id'))
		{

			$customer_id = Session::get('id');
			$orders = Store_model::get_orders($customer_id);
			if($orders){
				foreach ($orders as $order) {
					$product[$order->id] = Store_model::get_order_items($order->id);
				}

				$data['products'] = $product;
				$data['orders'] = $orders;
				$data['INOUT'] = 'Logout';
				$data['session_name'] = Session::get('name');
				$data['session_email'] = Session::get('email');
				$data['session_id'] = Session::get('id');
			}else{
				session(['MESSAGE' => 'You haven"t done any purchases yet, shop to see your orders']);
				session(['thegif' => 'error2']);
				return redirect('store/message');
			}

		}
		else
		{
			session(['MESSAGE' => 'You must sign in in order to check your recent orders']);
			session(['thegif' => 'error2']);
			return redirect('store/message');
		}


		if(session()->has('cart'))
		{
			$cart = Session::get('cart');
			$data['items_in_cart'] = Session::get('items_in_cart');
			$data['total'] = Session::get('total');
		}
		else{
			$data['items_in_cart'] = '0';
			$data['total'] = '0';
		}

		return view('orders', $data);
	}

}
