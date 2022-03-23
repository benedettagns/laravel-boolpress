<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Tag;
use App\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::where("user_id", Auth::user()->id)->get();
        return view("admin.post.index", compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view("admin.posts.create", compact("categories", "tags"));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate( [
            "title" => "required|min:5",
            "content" => "required|min:20",
            "category_id" => "nullable", 
            "tags" => "nullable"
        ]);

        $post = new Post();
        $post->fill($data);

        $slug = Str::slug($post->title);

        $exists = Post::where("slug", $slug)->first();
        $counter = 1;

        while ($exists) {
            $newSlug = $slug . "-" . $counter;
            $counter++;

            $exists = Post::where("slug", $newSlug)->first();

            if (!$exists) {
                $slug = $newSlug;
            }
        }

        $post->slug = $slug;
        $post->user_id = Auth::user()->id;
        $post->save();
        $post->tags()->attach($data["tags"]);
        return redirect()->route("admin.posts.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::where("slug", $slug)->fisrt();

        return view("admin.post.show", compact("post"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $post = Post::where("slug", $slug)->first();

        $categories = Category::all();
        $tags = Tag::all();
        return view("admin.posts.edit", [
            "post" => $post, 
            "categories" => $categories,
            "tags" => $tags
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            "title" => "required|min:5", 
            "content" => "required|min:20",
            "category_id" => "nullable|exists:categories,id",
            "tags" => "nullable|exists:tags,id"
        ]);

        $post = Post::findOrFail($id);

        if ($data["title"] !== $post->title) {
            $data["slug"] = $this->generateUniqueSlug($data["title"]);
        }

        $post->update($data);
        if(key_exists("taga", $data)) {
            $post->tags()->route("admin.posts.show", $post->slug);
        }
        return redirect()->route("admin.posts.show", $post->slug);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->tags()->detach();
        $post->delete();
    }

    protected function generateUniqueSlug($postTitle) {
        $slug = Str::slug($postTitle);

        $exists = Post::where("slug", $slug)->first();

        $counter = 1;
        
        while($exists) {
            $newSlug = $slug . "-" . $counter;
            $counter++;

            $exists = Post::where("slug", $newSlug)->first();

            if (!$exists) {
                $slug = $newSlug;
            }
        }
        return $slug;
    }
}