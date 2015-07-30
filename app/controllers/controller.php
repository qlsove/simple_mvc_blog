<?php

class Controller{

	protected $connect;
	protected $db;

	public function __construct($host="localhost", $user="root", $password="123qwASD", $db="blog"){
		$this->connect=new Model($host, $user, $password, $db);
		$this->db=$this->connect->connect();
	}


	public function main(){
		if(!isset($_GET['action']) && !isset($_POST['action']))
			$this->get_blogs();
		else{
			$function=isset($_GET['action'])? $_GET['action'] : $_POST['action'];
				if(method_exists('Controller', $function))
					$this->$function();
			}
		

		}
		
		

	public function in_out(){
		if(isset($_GET['action']) && $_GET['action']=='in_out'){
			setcookie("login","", 1);
			header( "Location:index.php");
		}
		if(isset($_POST['in_out'])){
			$result=$this->connect->login($_POST["login"], $_POST["password"]);
				if(is_array($result)){
					setcookie("login", $result['login']);
					header('Location: index.php');
				}
				else{
					return "Неправильний логін або пароль";
				} 
		}
	}


	public function header(){
		$result=$this->connect->get_header();
			if(is_array($result)){
				include ("app/view/header.php");
			}
			else{
				echo "Не існує жодна категорія";
			} 
	}


	public function insert(){
			if(isset($_GET['action']) && $_GET['action']=='insert'){
				$category=$this->connect->get_header();
				include ("app/view/insert.php");
			}
			if(isset($_POST['action']) && $_POST['action']=='insert'){
				$result=$this->connect->insert_blog($_POST['name'], $_POST['body'], $_POST['category'], $_COOKIE['login'], $_POST['tags']);
				header( 'Location: index.php?action=admin', true); 
			}
	}


	public function change(){
			if(isset($_GET['action']) && $_GET['action']=='change'){
				$post=$this->connect->get_once_blog($_GET['id']);
				$category=$this->connect->get_header();
				include ("app/view/change.php");
			}
			if(isset($_POST['action']) && $_POST['action']=='change'){
				$this->connect->update($_POST['id'], $_POST['name'], $_POST['body'], $_POST['category'], $_COOKIE['login'], $_POST['tags']);
				header( 'Location: index.php?action=admin', true); 
			}
	}


	public function delete(){
		$result=$this->connect->delete($_GET['id']);
		header( 'Location: index.php?action=admin', true); 
	}


	public function get_blogs(){
		$posts=$this->connect->get_all_blogs();
			if($posts)
				include ("app/view/posts.php");
			else
				echo 'Блогів не знайдено!';
	}


	public function category(){
		$category=$this->connect->get_category($_GET['id']);
		$posts=$this->connect->get_once_category($category);
			if($posts)
				include ("app/view/posts.php");
			else
				echo 'Блогів даної категорії не знайдено!';
	}


	public function admin(){
		$posts=$this->connect->get_all_blogs();
		include ("app/view/admin.php");
	}
}


?>