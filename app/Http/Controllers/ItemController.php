<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::select('id', 'name', 'stock', 'image')->get();
        return response([
            'data' => $items
        ]);
    }

    public function show($id)
    {
        $item = Item::find($id);
        return response([
            'data' => $item
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'stock' => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,jpeg,png'
        ]);

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;

            Storage::disk('public')->putFileAs('items', $file, $newName);
            $request['image'] = $newName;
        }

        $item = Item::create($request->all());
    }

    // desttoy
    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();

        // delete image
        Storage::disk('public')->delete('items/' . $item->image);

        return response([
            'data' => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'stock' => 'required|integer',
            // 'image_file' => 'nullable|mimes:jpg,jpeg,png'
        ]);

        if ($request->file('image_file')) {

            // hapus file image yang lama
            $item = Item::find($id);
            Storage::disk('public')->delete('items/' . $item->image);

            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;

            Storage::disk('public')->putFileAs('items', $file, $newName);
            $request['image'] = $newName;
        }

        $item = Item::find($id);
        $item->update($request->all());

        return response([
            'data' => $item
        ]);
    }
}
