<?php

namespace App\Http\Controllers;
// import Model Blog, menggunakan model ini untuk menampilkan data
use App\Models\Blog;
// import Http Request, mendapatkan data request dari sisi client untuk di masukkan di dalam database
use Illuminate\Http\Request;
// import Facades Storage, melakukan store atau upload data gambar ke dalam server
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    // fungsi untuk menampilkan data blog
    public function index()
    {
        // memanggil Model Blog kemudian mengurutkan datanya berdasarkan terbaru
        $blogs = Blog::latest()->paginate(10);
        return view('blog.index', compact('blogs'));
    }
    /**
    * create
    *
    * @return void
    */
    // menampilkan form
    public function create()
    {
        return view('blog.create');
    }


    /**
    * store
    *
    * @param  mixed $request
    * @return void
    */
    // melakukan proses insert data ke dalam database
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'title'     => 'required',
            'content'   => 'required'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/blogs', $image->hashName());

        $blog = Blog::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        if($blog){
            //redirect dengan pesan sukses
            return redirect()->route('blog.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('blog.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }
    /**
    * edit
    *
    * @param  mixed $blog
    * @return void
    */
    public function edit(Blog $blog)
    {
        return view('blog.edit', compact('blog'));
    }


    /**
    * update
    *
    * @param  mixed $request
    * @param  mixed $blog
    * @return void
    */
    public function update(Request $request, Blog $blog)
    {
        $this->validate($request, [
            'title'     => 'required',
            'content'   => 'required'
        ]);

        //get data blog dengan id
        $blog = Blog::findOrFail($blog->id);

        if($request->file('image') == "") {

            $blog->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        } else {

            //hapus old image
            Storage::disk('local')->delete('public/blogs/'.$blog->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/blogs', $image->hashName());

            $blog->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        }

        if($blog){
            //redirect dengan pesan sukses
            return redirect()->route('blog.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('blog.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }
    /**
    * destroy
    *
    * @param  mixed $id
    * @return void
    */
    public function destroy($id)
    {
    $blog = Blog::findOrFail($id);
    Storage::disk('local')->delete('public/blogs/'.$blog->image);
    $blog->delete();

    if($blog){
        //redirect dengan pesan sukses
        return redirect()->route('blog.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('blog.index')->with(['error' => 'Data Gagal Dihapus!']);
    }
    }
}
