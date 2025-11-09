<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function index()
    {
        $sources = Source::where('enabled', true)->get(['id', 'name', 'slug']);
        return response()->json($sources);
    }
}
