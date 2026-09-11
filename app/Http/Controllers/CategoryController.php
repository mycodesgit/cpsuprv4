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
use App\Models\AuditTrailCategory;  

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('user')->get();

        return view('pages.manage.category', compact('categories'));
    }

    public function show()
    {
        $data = Category::with('user')->where('cstatus', 1)->get();

        return response()->json(['data' => $data]);
    }

    public function create(Request $request) 
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'category_name' => 'required',
            ]);

            $categoryName = $request->input('category_name'); 
            $existingCategory = Category::where('category_name', $categoryName)->first();

            if ($existingCategory) {
                return redirect()->route('categoryRead')->with('error1', 'Category already exists!');
            }

            try {
                $cat = Category::create([
                    'user_id' => Auth::user()->id,
                    'category_name' => $request->input('category_name'),
                    'isICT' => $request->input('isICT'),
                ]);

                $userPayload = $cat->toArray();

                $this->logAudit($request, 'Add_Category', $userPayload);
                
                return response()->json(['success' => true, 'message' => 'Category stored successfully!'],  200);

            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => 'Failed to store Category!'],  404);
            }
        }
    }

    public function update(Request $request) 
    {
        $request->validate([
            'id' => 'required',
            'category_name' => 'required',
        ]);

        try {
            $categoryName = $request->input('category_name');
            $existingCategory = Category::where('category_name', $categoryName)->where('id', '!=', $request->input('id'))->first();

            if ($existingCategory) {
                return response()->json(['error' => true, 'message' => 'Category already exists!'], 200);
            }

            $category = Category::findOrFail($request->input('id'));
            $category->update([
                'user_id' => Auth::user()->id,
                'category_name' => $categoryName,
                'isICT' => $request->input('isICT'),
            ]);

            $newData = $category->fresh()->toArray();

            $auditPayload = [
                'after' => $newData,
            ];

            $this->logAudit($request, 'Edit_Category', $auditPayload);

            return response()->json(['success' => true, 'message' => 'Updated Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'Failed to update Category!'], 404);
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

        AuditTrailCategory::create([
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
