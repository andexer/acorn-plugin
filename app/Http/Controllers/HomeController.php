<?php

namespace App\Http\Controllers;

class HomeController
{
	public function __construct()
	{
		add_shortcode('acorn_home', [$this, 'index']);
	}
	public function index()
	{
		return view('home');
	}
}
