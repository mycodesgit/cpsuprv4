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
use App\Models\YearPr;        
use App\Models\AuditTrailYearPr; 

class YearController extends Controller
{
    public function index()
    {
        return view('pages.manage.year');
    }

    public function show() 
    {
        $data = YearPr::orderBy('pryear', 'ASC')->get();

        return response()->json(['data' => $data]);
    }

    public function create(Request $request) 
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'pryear' => 'required',
            ]);

            $yearName = $request->input('pryear'); 
            $existingYear = YearPR::where('pryear', $yearName)->first();

            if ($existingYear) {
                return response()->json(['error' => true, 'message' => 'Year already exists!'],  404);
            }

            try {
                $yrpr = YearPr::create([
                    'user_id' => Auth::user()->id, 
                    'pryear'  => $yearName,
                ]);

                $userPayload = $yrpr->toArray();
                $this->logAudit($request, 'Add_Year', $userPayload);

                return response()->json(['success' => true, 'message' => 'Year stored successfully!'],  200);
            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => 'Failed to add Year!'],  404);
            }
        }
    }

    public function update(Request $request) 
    {
        $request->validate([
            'id' => 'required',
            'pryear' => 'required',
        ]);

        try {
            $prsetYear = $request->input('pryear');
            $existingYear = YearPR::where('pryear', $prsetYear)->where('id', '!=', $request->input('id'))->first();

            if ($existingYear) {
                return response()->json(['error' => true, 'message' => 'Office already exists!'], 200);
            }

            $yrpr = YearPr::findOrFail($request->input('id'));
            $yrpr->update([
                'user_id' => Auth::user()->id, 
                'pryear'  => $prsetYear,
                'status'  => $request->input('status'),
            ]);

            $newData = $yrpr->fresh()->toArray();

            $auditPayload = [
                'after' => $newData,
            ];

            $this->logAudit($request, 'Edit_Year', $auditPayload);

            return response()->json(['success' => true, 'message' => 'Updated Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'Failed to update Year!'], 404);
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

        AuditTrailYearPr::create([
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
