<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Jenssegers\Agent\Agent;

use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use App\Models\AuditTrailItem;

class ItemController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $units = Unit::all();

        return view('pages.manage.item', compact('categories', 'units'));
    }

    public function show()
    {
        $data = Item::with('user', 'category', 'unit')->get();

        return response()->json(['data' => $data]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'category_id' => 'required',
                'unit_id' => 'required',
                'item_description' => 'required',
                'estimated_cost' => 'required',
            ]);

            $itemName = $request->input('item_description');
            $existingItem = Item::where('item_description', $itemName)->first();

            if ($existingItem) {
                return response()->json(['error' => true, 'message' => 'Item already exists!']);
            }

            // 1. Fetch Category and Unit models to retrieve cname and uname
            $category = Category::find($request->input('category_id'));
            $unit = Unit::find($request->input('unit_id'));

            // 2. Strip commas from formatted currency before saving to decimal column
            $cleanCost = str_replace(',', '', $request->input('estimated_cost'));

            try {
                $item = Item::create([
                    'user_id'          => Auth::user()->id,
                    'category_id'      => $category->id,
                    'unit_id'          => $unit->id,
                    'cname'            => $category->category_name,
                    'uname'            => $unit->unit_name,
                    'item_description' => $request->input('item_description'),
                    'estimated_cost'   => (float) $cleanCost,
                ]);

                $userPayload = $item->toArray();
                $this->logAudit($request, 'Add_Item', $userPayload);

                return response()->json(['success' => true, 'message' => 'Item stored successfully!']);
            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => 'Failed to store Item!']);
            }
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'unit_id' => 'required',
            'item_description' => 'required',
            'estimated_cost' => 'required',
        ]);

        try {
            $itemDescription = $request->input('item_description');
            $existingItem = Item::where('item_description', $itemDescription)->where('id', '!=', $request->input('id'))->first();

            if ($existingItem) {
                return response()->json(['error'=> true, 'message' => 'Item already exists!']);
            }

            // 1. Fetch Category and Unit models to retrieve cname and uname
            $category = Category::find($request->input('category_id'));
            $unit = Unit::find($request->input('unit_id'));

            // 2. Strip commas from formatted currency before saving to decimal column
            $cleanCost = str_replace(',', '', $request->input('estimated_cost'));

            $item = Item::findOrFail($request->input('id'));

            // 3. Perform update including cname and uname
            $item->update([
                'user_id'          => Auth::user()->id,
                'category_id'      => $category->id,
                'unit_id'          => $unit->id,
                'cname'            => $category->category_name,
                'uname'            => $unit->unit_name,
                'item_description' => $itemDescription,
                'estimated_cost' => (float) $cleanCost,
            ]);

            $newData = $item->fresh()->toArray();

            $auditPayload = [
                'after' => $newData,
            ];

            $this->logAudit($request, 'Edit_item', $auditPayload);

            return response()->json(['success' => true, 'message' => 'Updated Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'Failed to update Item!']);
        }
    }

    /**
     * Helper function to centralize audit trail logging.
     */
    private function logAudit(Request $request, string $action, array $payload): void
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        $browser  = $agent->browser();
        $platform = $agent->platform();

        AuditTrailItem::create([
            'user_id'    => auth()->id(),
            'username'   => auth()->user()->username ?? 'System',
            'action'     => $action,
            'actiondata' => json_encode($payload),
            'ip_address' => $request->ip(),
            'user_agent' => $browser . ' on ' . $platform,
            'login_at'   => now(),
        ]);
    }
}
