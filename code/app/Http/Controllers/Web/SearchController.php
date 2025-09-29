<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('pfe.search.index');
    }

    public function search(Request $request)
    {
        return view('pfe.search.results');
    }

    public function quick(Request $request)
    {
        return response()->json([
            'results' => []
        ]);
    }

    public function advanced(Request $request)
    {
        return view('pfe.search.advanced');
    }
}