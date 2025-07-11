<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\GalleryPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // ✅ Get all posts
    public function index()
    {
        $posts = Post::with('gallery')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Posts retrieved successfully',
            'data' => $posts
        ]);
    }

    // ✅ Show single post
    public function show($id)
    {
        $post = Post::with('gallery')->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post retrieved successfully',
            'data' => $post
        ]);
    }

    // ✅ Create post with media
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'date' => 'required|date',
            'heure' => 'required',
            'status' => 'in:active,archive',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'description' => $request->description,
            'date' => $request->date,
            'heure' => $request->heure,
            'status' => $request->status ?? 'active',
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                GalleryPost::create([
                    'post_id' => $post->id,
                    'img' => $path
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post->load('gallery')
        ], 201);
    }

    // ✅ Update post and optionally add media
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'heure' => 'sometimes',
            'status' => 'in:active,archive',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $post->update($request->only(['description', 'date', 'heure', 'status']));

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                GalleryPost::create([
                    'post_id' => $post->id,
                    'img' => $path
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post->load('gallery')
        ]);
    }

    // ✅ Delete post and its media
    public function destroy($id)
    {
        $post = Post::with('gallery')->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        foreach ($post->gallery as $media) {
            if (Storage::disk('public')->exists($media->img)) {
                Storage::disk('public')->delete($media->img);
            }
            $media->delete();
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post and its media deleted successfully'
        ]);
    }
}
