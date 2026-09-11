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
use App\Models\Unit;        
use App\Models\AuditTrailUnit;  

class UnitController extends Controller
{
    public function index()
    {
        return view('pages.manage.unit');
    }

    public function show()
    {
        $data = Unit::with('user')->get();

        return response()->json(['data' => $data]);
    }

    public function create(Request $request) 
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'unit_name' => 'required',
            ]);

            $unitName = $request->input('unit_name'); 
            $existingUnit = Unit::where('unit_name', $unitName)->first();

            if ($existingUnit) {
                return response()->json(['error' => true, 'message' => 'Unit already exists!']);
            }

            try {
                $unit = Unit::create([
                    'user_id' => Auth::user()->id,
                    'unit_name' => $request->input('unit_name'),
                    'remember_token' => Str::random(60),
                ]);

                $userPayload = $unit->makeHidden(['remember_token'])->toArray();

                $this->logAudit($request, 'Add_Unit', $userPayload);

                return response()->json(['success' => true, 'message' => 'Unit stored successfully!']);
            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => 'Failed to store Unit!']);
            }
        }
    }

    public function update(Request $request) 
    {
        $request->validate([
            'id' => 'required',
            'unit_name' => 'required',
        ]);

        try {
            $unitName = $request->input('unit_name');
            $existingUnit = Unit::where('unit_name', $unitName)->where('id', '!=', $request->input('id'))->first();

            if ($existingUnit) {
                return response()->json(['error' => true, 'message' => 'Unit already exists!']);
            }

            $unit = Unit::findOrFail($request->input('id'));
            $unit->update([
                'unit_name' => $unitName,
            ]);

            $newData = $unit->fresh()->toArray();

            $auditPayload = [
                'after' => $newData,
            ];

            $this->logAudit($request, 'Edit_Unit', $auditPayload);

            return response()->json(['success' => true, 'message' => 'Unit Updated Successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'Failed to update Unit!']);
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

        AuditTrailUnit::create([
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
