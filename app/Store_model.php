<?php

namespace App;

use Illuminate\Support\Facades\DB; 

class Store_model
{
	public static function get_all_products()
	{
		$products = DB::select("SELECT products.description, products.id, products.image, products.name, products.price, categories.id as categoriesid, categories.name as catname, categories.image as catimage FROM products INNER JOIN categories ON products.cat_id=categories.id ORDER BY products.id ASC");
		return $products;
	}

	public static function get_products($id)
	{
		$products = DB::select("SELECT products.description, products.id, products.image, products.name, products.price, categories.id as categoriesid, categories.name as catname, categories.image as catimage FROM products INNER JOIN categories ON products.cat_id=categories.id WHERE categories.id=$id");
		return $products;
	}  

	public static function get_product($product_id)
	{
		$product = "SELECT products.description, products.id, products.image, products.name, products.price, categories.id as categoriesid, categories.name as catname, categories.image as catimage FROM products INNER JOIN categories ON products.cat_id=categories.id WHERE products.id='$product_id'";
		$query = DB::select($product);
		if (!$query) return false;
		else{
			$first_row = $query[0];
			$id = $first_row->id;
			$name = $first_row->name;
			$description = $first_row->description;
			$categorie = $first_row->catname;
			$price = $first_row->price;
			$image = $first_row->image;
			return $first_row;
		}
	}  

	public static function get_categories()
	{
		$categories = DB::select("SELECT * FROM categories");
		return $categories;
	}  

	public static function register_user($name, $email, $password, $passwordconfirmation)
	{
		$encryptpass = substr(md5($passwordconfirmation),0,32);
		$sql_insert = "INSERT INTO customers (name,email,created_at,updated_at,password_digest,remember_digest) VALUES ('$name','$email',NOW(),NOW(),'$encryptpass','$encryptpass')"; 
		DB::insert($sql_insert);

		return true;
	} 

	public static function login($email, $password)
	{

		$sql = "SELECT * FROM customers WHERE email='" . $email . "' AND password_digest='" . substr(md5($password),0,32) . "'";
		$query = DB::select($sql);
		if (!$query) return false;
		else{
			$first_row = $query[0];
			$id = $first_row->id;
			$name = $first_row->name;
			$email = $first_row->email;                   
			return $first_row;
		}
	} 

	public static function set_remember_digest($email,$remember_digest)
	{
		$sql_insert = "UPDATE customers SET remember_digest='$remember_digest' WHERE email='$email'";
		DB::update($sql_insert);
		return true;
	}

	public static function check_remember_digest($remember_digest)
	{
		$sql = "SELECT id,name,email FROM customers WHERE remember_digest='$remember_digest'";
		$query = DB::select($sql);
		if (!$query) return false;
		else{
			$first_row = $query[0];
			$id = $first_row->id;
			$name = $first_row->name;
			$email = $first_row->email;                   
			return $first_row;
		}
	}  


	public static function create_order($customer_id, $total) 
	{
		$sql_insert = "INSERT INTO orders(customer_id, created_at, status,total) VALUES ('$customer_id', NOW(), 1, '$total')"; 
		DB::insert($sql_insert);
		return true;
	}



	public static function insert_order_item($order_id, $product_id, $quantity) 
	{
		$sql_insert = "INSERT INTO order_items(order_id, product_id, quantity) VALUES ('$order_id', '$product_id', '$quantity' ) ON DUPLICATE KEY UPDATE quantity = quantity + 1"; 
		DB::insert($sql_insert);

		return true;
	}



	public static function checkorder($customer_id, $status){
		$sql = "SELECT MAX(id) as id FROM orders WHERE customer_id='$customer_id' AND status='$status'";
		$query = DB::select($sql);
		if (!$query) return false;
		else{                  
			$first_row = $query[0];
			$id = $first_row->id;                
			return $first_row;
		}

}	

public static function get_orders($customer_id){
		$sql = "SELECT * FROM orders WHERE customer_id='$customer_id' ORDER BY created_at DESC";
		$query = DB::select($sql);
		return $query;
	}

public static function get_order_items($order_id){
		$sql = "SELECT products.name, products.image, quantity, price,categories.name as catname,quantity * price as subtotal, products.description FROM order_items, products, orders, categories WHERE orders.id = '$order_id' AND order_items.product_id=products.id AND order_items.order_id=orders.id AND products.cat_id=categories.id";
		$orders = DB::select($sql);
		return $orders;
	}		

/*

	public static function checkproductid($product_id, $order_id)
	{
		$sql = ("SELECT id FROM order_items WHERE product_id='$product_id' AND order_id='$order_id'");
		$query = DB::select($sql);
		if (!$query) return false;
		else{
			$first_row = $query[0];
			$id= $first_row->id;                   
			return $first_row;
		}
	}


	public static function itemsincart($order_id)
	{
		$items = "SELECT SUM(quantity) as sum FROM order_items WHERE order_id='$order_id'";
		$query = DB::select($items);
		if (!$query) return 0;
		else{
			$first_row = $query[0];
			$sum = $first_row->sum;                   
			return $first_row;
		}
	}

	public static function update_quantity($product_id, $order_id) 
	{
		$sql_insert = "UPDATE order_items SET quantity = quantity + 1 WHERE product_id='$product_id' AND order_id='$order_id'";
		DB::update($sql_insert);
		return true;
	}

	public static function setcartforuser($customer_id, $status, $order_id) 
	{
		$sql_insert = "UPDATE orders SET customer_id='$customer_id',status='$status' WHERE id='$order_id'";
		DB::update($sql_insert);
		return true;
	}


	public static function totalize($order_id) 
	{
		$totalize = "SELECT SUM(products.price*order_items.quantity) as total FROM products,order_items WHERE products.id=order_items.product_id AND order_items.order_id=$order_id";
		$query = DB::select($totalize);
		if (!$query) return false;
		else{
			$first_row = $query[0];
			$total = $first_row->total;                   
			return $first_row;
		}
	}

	public static function checkorderstatus($customer_id, $status){
		$sql = "SELECT id,total FROM orders WHERE customer_id='$customer_id' AND status='$status'";
		$query = DB::select($sql);
		if (!$query) return false;
		else{                  
			$first_row = $query[0];
			$id = $first_row->id;
			$total = $first_row->total;                  
			return $first_row;
		}
	}

	public static function checkorderstatusforout($customer_id, $status){
		$sql = "SELECT id,total,MAX(created_at) FROM orders WHERE customer_id='$customer_id' AND status='$status' GROUP BY id, total";
		$query = DB::select($sql);
		if (!$query) return false;
		else{                  
			$first_row = $query[0];
			$id = $first_row->id;
			$total = $first_row->total;                  
			return $first_row;
		}

}*/
}