<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(): View
    {
        $motorcycles = Motorcycle::latest()->paginate(10);

        return view('admin.motorcycles.index', compact('motorcycles'));
    }

    public function create(): View
    {
        return view('admin.motorcycles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'brand'        => ['required', 'string', 'max:255'],
            'model'        => ['required', 'string', 'max:255'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:motorcycles,plate_number'],
            'year'         => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'price_per_day'=> ['required', 'numeric', 'min:0'],
            'status'       => ['required', 'in:available,rented,maintenance'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('motorcycles', 'public');
        }

        Motorcycle::create($validated);

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil ditambahkan.');
    }

    public function show(Motorcycle $motorcycle): View
    {
        return view('admin.motorcycles.show', compact('motorcycle'));
    }

    public function edit(Motorcycle $motorcycle): View
    {
        return view('admin.motorcycles.edit', compact('motorcycle'));
    }

    public function update(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'brand'        => ['required', 'string', 'max:255'],
            'model'        => ['required', 'string', 'max:255'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:motorcycles,plate_number,' . $motorcycle->id],
            'year'         => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'price_per_day'=> ['required', 'numeric', 'min:0'],
            'status'       => ['required', 'in:available,rented,maintenance'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($motorcycle->image) {
                Storage::disk('public')->delete($motorcycle->image);
            }
            $validated['image'] = $request->file('image')->store('motorcycles', 'public');
        }

        $motorcycle->update($validated);

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil diperbarui.');
    }

    public function destroy(Motorcycle $motorcycle): RedirectResponse
    {
        if ($motorcycle->image) {
            Storage::disk('public')->delete($motorcycle->image);
        }

        $motorcycle->delete();

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil dihapus.');
    }
}