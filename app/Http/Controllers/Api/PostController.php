<?php

namespace App\Http\Controllers\Api;

//Import post model
use App\Models\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Import post resource
use App\Http\Resources\PostResource;

//Import storage validator
use Illuminate\Support\Facades\Storage;

//Import facade validator
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
        //Get all posts
        $posts = Post::latest()->paginate(5);

        //Return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        //Define validation rules
        $validator = Validator::make($request->all(), [
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'   => 'required',
            'content' => 'required',
        ]);

        //Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * Show
     * 
     * @param mixed $post
     * @return void
     */
    public function show($id)
    {
        //Find post by id
        $post = Post::find($id);

        //Return single post as a resource
        return new PostResource(true, 'Detail data post!', $post);
    }

    /**
     * Update
     * 
     * @param mixed $request
     * @param mixed $post
     * @return void
     */
    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $post = Post::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.basename($post->image));

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);

        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    public function destroy($id)
    {
        //find post by ID
        $post = Post::find($id);

        //delete image
        Storage::delete('public/posts/'.basename($post->image));

        //delete post
        $post->delete();

        //return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
