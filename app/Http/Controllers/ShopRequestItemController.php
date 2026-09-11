<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;

class ShopRequestItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('items')->get();
        $query = Item::with(['category', 'unit']);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('item_description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                      $catQuery->where('category_name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $items = $query->paginate(6)->appends($request->query());
        $totalItemsCount = Item::count();

        $selectedCategory = $categories->firstWhere('id', $request->category);
        $currentCategoryName = ($request->filled('category') && $request->category !== 'all')
            ? ($selectedCategory ? $selectedCategory->category_name : 'All Items')
            : 'All Items';

        // Return pure JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'categoryName' => $currentCategoryName,
                'itemCountText' => "Showing " . ($items->firstItem() ?? 0) . "-" . ($items->lastItem() ?? 0) . " of " . $items->total() . " items",
                'items' => $items->items(),
                'pagination' => (string) $items->links('pagination::bootstrap-5')
            ]);
        }

        return view('pages.request.shop-request-item', compact('categories', 'items', 'totalItemsCount', 'currentCategoryName'));
    }

}