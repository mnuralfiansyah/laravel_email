<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Terbilang;

class TerbilangController extends Controller
{
	public function index()
	{
		$terbilang = Terbilang::make(123456, ' rupiah', 'senilai ');
		return $terbilang;
	}	
}
