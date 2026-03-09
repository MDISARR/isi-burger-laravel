<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBurgerRequest;
use App\Http\Requests\UpdateBurgerRequest;
use App\Models\Burger;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class BurgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $burgers = Burger::query()
            ->with('category')
            ->withExists('orderItems')
            ->latest()
            ->paginate(15);

        return view('admin.burgers.index', [
            'burgers' => $burgers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.burgers.create', [
            'burger' => new Burger(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBurgerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('burgers', 'public');
        }

        $data['is_archived'] = $request->boolean('is_archived');

        Burger::query()->create($data);

        return redirect()
            ->route('admin.burgers.index')
            ->with('success', 'Burger ajoute avec succes.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Burger $burger): View
    {
        return view('admin.burgers.edit', [
            'burger' => $burger,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBurgerRequest $request, Burger $burger): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($burger->image_path) {
                Storage::disk('public')->delete($burger->image_path);
            }

            $data['image_path'] = $request->file('image')->store('burgers', 'public');
        }

        $data['is_archived'] = $request->boolean('is_archived');

        $burger->update($data);

        return redirect()
            ->route('admin.burgers.index')
            ->with('success', 'Burger modifie avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Burger $burger): RedirectResponse
    {
        if ($burger->orderItems()->exists()) {
            $burger->update(['is_archived' => true]);

            return back()->with('success', 'Burger archive car il est lie a des commandes.');
        }

        if ($burger->image_path) {
            Storage::disk('public')->delete($burger->image_path);
        }

        $burger->delete();

        return back()->with('success', 'Burger supprime avec succes.');
    }

    public function restore(Burger $burger): RedirectResponse
    {
        $burger->update(['is_archived' => false]);

        return back()->with('success', 'Burger restaure avec succes.');
    }
}
