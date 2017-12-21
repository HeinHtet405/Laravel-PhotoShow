<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Photo;


class PhotosController extends Controller
{
    public function create($album_id) {
       return view('photos.create')->with('album_id', $album_id);
    }

    public function store(Request $request) {
      $this->validate($request, [
         'title' => 'required',
         'photo' => 'image|max:1999'
      ]);

      // Get filename with extension
      $filenameWithExt = $request->file('photo')->getClientOriginalName();

      // Get just the filename
      $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

      // Get extnesion
      $extension = $request->file('photo')->getClientOriginalExtension();

      // Create New File name
      $filnameToStore = $filename . '_' .time(). '.'.$extension;

      // Upload image
      // image path --> http://photoshow.dev/storage/album_covers/acc_1511166303.jpg
      $path = $request->file('photo')->storeAs('public/photos/'.$request->input('album_id'), $filnameToStore);

      // Upload photo
      $photo = new Photo;
      $photo->album_id = $request->input('album_id');
      $photo->title = $request->input('title');
      $photo->description = $request->input('description');
      $photo->size = $request->file('photo')->getClientSize();
      $photo->photo = $filnameToStore;

      $photo->save();

      return redirect('/albums/'.$request->input('album_id'))->with('success','Photo Uploaded');
    }

    public function show($id) {
      $photo = Photo::find($id);
      return view('photos.show')->with('photo', $photo);
    }

    public function destory($id){
       $photo = Photo::find($id);
       if (Storage::delete('public/photos/'.$photo->album_id.'/'.$photo->photo)) {
          $photo->delete();
          return redirect('/')->with('success', 'Photo Delete');
       }
    }

}
