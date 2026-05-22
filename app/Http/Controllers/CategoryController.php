<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function __construct()
    {
        
        $this->middleware('permission:categories.view')
            ->only(['index', 'show']);

        $this->middleware('permission:categories.add')
            ->only(['create', 'store']);

        $this->middleware('permission:categories.modify')
            ->only(['edit', 'update']);

        $this->middleware('permission:categories.delete')
            ->only(['destroy']);
    }

    public function index()
    {
        $categories = Category::query()
            ->latest()
            ->paginate(20);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    // Optional: show page if you want
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }
}


// // $categories = \App\Models\Category::where('is_active', true)
//     ->orderBy('name')
//     ->get();
//     <select name="category_id" class="form-control form-control-sm">
//     <option value="">-- Select --</option>
//     @foreach($categories as $cat)
//         <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->ewc_code }})</option>
//     @endforeach
// </select>