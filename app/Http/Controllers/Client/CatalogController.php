<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Burger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Burger::query()
            ->with('category')
            ->clientVisible();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search')->toString().'%');
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        $burgers = $query
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('client.catalog.index', [
            'burgers' => $burgers,
            'filters' => $request->only(['search', 'price_min', 'price_max']),
        ]);
    }

    public function show(Burger $burger): View
    {
        abort_if($burger->is_archived, 404);

        return view('client.catalog.show', [
            'burger' => $burger->load('category'),
        ]);
    }
}
