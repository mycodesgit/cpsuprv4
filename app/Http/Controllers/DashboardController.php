<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\YearPr;

class DashboardController extends Controller
{
    /**
     * Months label Jan - Dec for the "Purchase Requests Submitted" card.
     */
    public const CHART_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $monthly = $this->getMonthlyCounts($year);

        return view('pages.home.dashboard', [
            'selectedYear'     => (int) $year,
            'chartMonths'      => self::CHART_MONTHS,
            'chartPending'     => $monthly['pending'],
            'chartApproved'    => $monthly['approved'],
        ]);
    }

    /**
     * JSON feed for the ApexCharts column chart.
     * GET /dashboard/chart-data?year=2026
     */
    public function chartData(Request $request)
    {
        $year = $request->input('year', date('Y'));

        return response()->json(array_merge(
            ['year' => (int) $year, 'months' => self::CHART_MONTHS],
            $this->getMonthlyCounts($year)
        ));
    }

    /**
     * Build [pending x12, approved x12] for a given year.
     *
     * Currently there is no dedicated purchase_requests table in this
     * codebase (only `yearpr` for year management), so this falls back to
     * sample data. Once a real PR table exists, point $table / columns
     * below at it and the chart + year filter will go live automatically.
     */
    private function getMonthlyCounts($year): array
    {
        // Attempt live query if a PR-like table ever appears.
        foreach (['purchase_requests', 'prs', 'purchase_request'] as $table) {
            if (Schema::hasTable($table)) {
                return $this->countFromTable($table, $year);
            }
        }

        // No PR table yet: deterministic sample data so the grouped
        // column chart (Pending vs Approved, Jan-Dec) still renders.
        // Seeded by year so changing the Year dropdown visibly changes bars.
        srand((int) $year);
        $pending = [];
        $approved = [];
        for ($m = 1; $m <= 12; $m++) {
            $pending[]  = rand(4, 22);
            $approved[] = rand(10, 38);
        }
        srand();

        // Override with a fixed realistic curve for the current year so
        // first load looks stable (still 2 bars per month).
        if ((int) $year === (int) date('Y')) {
            $pending  = [8, 12, 9, 14, 11, 16, 13, 18, 12, 15, 10, 9];
            $approved = [22, 28, 25, 31, 29, 35, 33, 38, 30, 34, 27, 24];
        }

        return ['pending' => $pending, 'approved' => $approved];
    }

    /**
     * Generic counter for a real PR table with `created_at`-style
     * timestamp + `status` column. Adjust status values to match
     * your workflow (pending vs approved).
     */
    private function countFromTable(string $table, $year): array
    {
        $pending = array_fill(0, 12, 0);
        $approved = array_fill(0, 12, 0);

        $dateColumn = Schema::hasColumn($table, 'created_at') ? 'created_at'
            : (Schema::hasColumn($table, 'submitted_at') ? 'submitted_at' : null);

        if (!$dateColumn) {
            return ['pending' => $pending, 'approved' => $approved];
        }

        $rows = \DB::table($table)
            ->selectRaw("MONTH({$dateColumn}) as m, status, COUNT(*) as c")
            ->whereYear($dateColumn, $year)
            ->groupBy('m', 'status')
            ->get();

        foreach ($rows as $row) {
            $idx = ((int) $row->m) - 1;
            if ($idx < 0 || $idx > 11) {
                continue;
            }
            $status = strtolower((string) $row->status);
            if (in_array($status, ['approved', 'completed', 'passed', '1', 'success'], true)) {
                $approved[$idx] += (int) $row->c;
            } else {
                $pending[$idx] += (int) $row->c;
            }
        }

        return ['pending' => $pending, 'approved' => $approved];
    }
}
