<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Album;

class AlbumsController extends Controller
{
    public function index() {
      $albums = Album::with('Photos')->get();
      return view('albums.index')->with('albums', $albums);
    }

    public function create() {
      return view('albums.create');
    }

    public function store(Request $request) {
       $this->validate($request, [
          'name' => 'required',
          'cover_image' => 'image|max:1999'
       ]);

       // Get filename with extension
       $filenameWithExt = $request->file('cover_image')->getClientOriginalName();

       // Get just the filename
       $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

       // Get extnesion
       $extension = $request->file('cover_image')->getClientOriginalExtension();

       // Create New File name
       $filnameToStore = $filename . '_' .time(). '.'.$extension;

       // Upload image
       // image path --> http://photoshow.dev/storage/album_covers/acc_1511166303.jpg
       $path = $request->file('cover_image')->storeAs('public/album_covers', $filnameToStore);

       // Create album
       $album = new Album;
       $album->name = $request->input('name');
       $album->description = $request->input('description');
       $album->cover_image = $filnameToStore;

       $album->save();

       return redirect('/albums')->with('success','Album Created');
    }

    public function show($id) {
        $album = Album::with('Photos')->find($id);
        return view('albums.show')->with('album', $album);
    }
}
