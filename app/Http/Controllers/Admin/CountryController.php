<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class CountryController extends Controller
{
    //
    public function index()
    {
        $countries = Country::all();
        return view('admin.country.index', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',

        ]);
        Country::create($request->all());
        return redirect()
        ->route('admin.country.index')
        ->with('success', 'thêm quốc gia thành công.');
    }

    public function edit($id)
    {
        $country = Country::findOrFail($id);
        return view('admin.country.edit', compact('country'));
    }

    public function delete($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();
        return redirect()
        ->route('admin.country.index')
        ->with('success', 'Xóa quốc gia thành công.');
    }
}
