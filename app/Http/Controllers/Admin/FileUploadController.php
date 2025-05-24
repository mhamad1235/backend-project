<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Image;
use Yajra\DataTables\Facades\DataTables;

class FileUploadController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Image::select('id', 'path', 'created_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    // Ensure $row->path has the correct file path and return it as a string
                    $url = Storage::url($row->path);  // Generate the URL using the Storage facade
                    return $url;  // Return the URL as a string
                })
                ->addColumn('action', function ($row) {
                    // Concatenate the $url and $row->path directly within the string
                    $url = Storage::url($row->path); // Generate URL for image
                    return '<a href="' . $url . '" class="btn btn-sm btn-primary">View</a>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->rawColumns(['action']) // No need to treat 'image' as raw column since it's now a string
                ->make(true);
        }

        return view('admin.images.index');
    }




public function data(Request $request)
{
    $data = Image::select('id', 'path', 'created_at');

    return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('image', function ($row) {
            $url = asset($row->path);
            return '<img src="' . $url . '" width="60" height="60" class="rounded" />';
        })
        ->addColumn('action', function ($row) {
            return '<a href="#" class="btn btn-sm btn-primary">View</a>';
        })
        ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
        ->rawColumns(['image', 'action'])
        ->make(true);
}


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $path = Storage::disk('s3')->put('uploads', $request->file('file'));
        $image = Image::create([
            'path' => $path
        ]);
        $url = Storage::disk('s3')->url($path);

        return back()->with('success', 'File uploaded successfully!')->with('url', $url);
    }
}
