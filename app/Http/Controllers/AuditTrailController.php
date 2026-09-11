<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AuditTrailUser;
use App\Models\AuditTrailCategory;
use App\Models\AuditTrailUnit;
use App\Models\AuditTrailItem;
use App\Models\AuditTrailOffice;
use App\Models\AuditTrailYearPr;

class AuditTrailController extends Controller
{
    public function index()
    {
        return view('pages.audit-trail.index');
    }

    public function showUser()
    {
        $data = AuditTrailUser::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function showCategory()
    {
        $data = AuditTrailCategory::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $data]);
    }
    
    public function showUnit()
    {
        $data = AuditTrailUnit::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $data]);
    }
    
    public function showItem()
    {
        $data = AuditTrailItem::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $data]);
    }
    
    public function showOffice()
    {
        $data = AuditTrailOffice::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $data]);
    }
    
    public function showYear()
    {
        $data = AuditTrailYearPr::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $data]);
    }
}
