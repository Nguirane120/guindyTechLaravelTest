<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        $data = $categories->map(function ($category) {
            return [
                'name' => $category->name,
                'description' => $category->description,
                'level' => $category->level,
                'parent' => $category->parent ? $category->parent->name : null,
                'product_count' => $category->products->count(),
            ];
        });

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

       
        $level = 0;
        if ($validated['parent_id']) {
            $parent = Category::find($validated['parent_id']);
            $level = $parent->level + 1;
        }

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
            'level' => $level,
        ]);
        
        

        return response()->json(['category' => $category], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::with('parent', 'children', 'products')->find($id);
    
        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }
    
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'level' => $category->level,
            'parent' => $category->parent ? $category->parent->name : null,
            'product_count' => $category->products->count(),
        ];
    
        return response()->json($data, 200);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
