@extends('layout.app')

@section('title')
    CPSU PR V.4 | Dashboard
@endsection

@section('body')
    @php
        $chartMonths ??= ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartPending ??= [8, 12, 9, 14, 11, 16, 13, 18, 12, 15, 10, 9];
        $chartApproved ??= [22, 28, 25, 31, 29, 35, 33, 38, 30, 34, 27, 24];
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="mb-4">

                <!-- Dashboard Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h1 class="h5 fw-bold mb-1">Dashboard</h1>
                        <p class="text-muted small mb-0">System metrics, departmental request analytics, and real-time activity logs.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <form id="yearForm" class="d-flex align-items-center gap-2">
                            <label for="yearSelect" class="form-label mb-0 small text-muted">Year:</label>
                            <select id="yearSelect" name="year" class="form-select form-select-sm">
                                @php $selectedYear = request('year', date('Y')); @endphp
                                @for ($y = date('Y'); $y >= date('Y') - 4; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Middle Row: Analytics Chart & Right Side Widgets -->
                <div class="row g-3">
                    <!-- Monthly Submissions Bar Chart -->
                    <div class="col-lg-9">
                        <!-- Top Row: Metric Cards -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="card card-animate h-100">
                                    <div class="card-body">
                                        <span class="text-muted small text-uppercase fw-semibold">Total Requests</span>
                                        <h2 class="fw-bold my-1">1,248</h2>
                                        <div class="d-flex align-items-center text-success small fw-semibold">
                                            <span>↑ 12.5% from last month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card card-animate h-100">
                                    <div class="card-body">
                                        <span class="text-muted small text-uppercase fw-semibold">Pending Verification</span>
                                        <h2 class="fw-bold text-warning my-1">42</h2>
                                        <span class="text-muted small">18 IT / 14 Budget / 10 Initial</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card card-animate h-100">
                                    <div class="card-body">
                                        <span class="text-muted small text-uppercase fw-semibold">Total Spend (YTD)</span>
                                        <h2 class="fw-bold my-1">$452,180</h2>
                                        <span class="text-muted small">84% of allocated budget</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card card-animate h-100">
                                    <div class="card-body">
                                        <span class="text-muted small text-uppercase fw-semibold">Approved Purchase Orders</span>
                                        <h2 class="fw-bold text-success my-1">892</h2>
                                        <span class="text-muted small">94.2% completion rate</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-animate mb-3">
                            <div class="card-header pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-semibold">
                                        <i class="ti ti-device-laptop me-1"></i> Purchase Requests Submitted (Jan 1 - Dec 31, <span id="displaySupportSelectedYear">{{ $selectedYear }}</span>)
                                    </h6>
                                    {{-- <span class="spinner-grow spinner-grow-sm text-success me-2" role="status"></span> --}}
                                    <span class="small">
                                        <span class="badge bg-light text-dark border">Jan - Dec</span>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div id="prSubmissionsChart"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Dynamic Calendar & Announcements -->
                    <div class="col-lg-3">
                        <div class="card card-animate">
                            <div class="card-body">
                                <!-- Dynamic Calendar Controls -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0 text-dark" id="calendarMonthYear">Month Year</h6>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-icon btn-light rounded-2 p-1 border" id="prevMonth">
                                            <i class="ti ti-chevron-left"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-light rounded-2 p-1 border" id="nextMonth">
                                            <i class="ti ti-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="shadcn-calendar">
                                    <div class="calendar-grid calendar-header text-muted small fw-medium mb-2">
                                        <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                                    </div>
                                    <!-- Dynamic Days Container -->
                                    <div class="calendar-grid calendar-days small" id="calendarDays">
                                    </div>
                                </div>

                                <hr class="my-3 text-muted opacity-25">

                                <!-- System Announcements Section -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark">Announcements</h6>
                                    <span class="badge bg-light text-secondary border">Top 5</span>
                                </div>

                                <div class="list-group list-group-flush border-0">
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                        <div class="text-truncate me-2" style="max-width: 190px;">
                                            <span class="d-block text-dark fw-medium small text-truncate">Q3 Budget Clearance Deadline</span>
                                            <small class="text-muted">Sep 15, 2026</small>
                                        </div>
                                        <button class="btn btn-sm btn-secondary py-0 px-2 text-nowrap" style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#announcementModal"
                                                data-title="Q3 Budget Clearance Deadline"
                                                data-date="Sep 15, 2026"
                                                data-content="All departments must submit their Q3 procurement clearing documents before September 15. Pending requests after this date will be shifted to Q4 allocation."><i class="ti ti-eye"></i></button>
                                    </div>

                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                        <div class="text-truncate me-2" style="max-width: 190px;">
                                            <span class="d-block text-dark fw-medium small text-truncate">Scheduled System Maintenance</span>
                                            <small class="text-muted">Sep 18, 2026</small>
                                        </div>
                                        <button class="btn btn-sm btn-secondary py-0 px-2 text-nowrap" style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#announcementModal"
                                                data-title="Scheduled System Maintenance"
                                                data-date="Sep 18, 2026"
                                                data-content="The Purchase Request system will undergo scheduled server maintenance on September 18 from 10:00 PM to 2:00 AM. Access may be intermittent during this window."><i class="ti ti-eye"></i></button>
                                    </div>

                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                        <div class="text-truncate me-2" style="max-width: 190px;">
                                            <span class="d-block text-dark fw-medium small text-truncate">New IT Procurement Guidelines</span>
                                            <small class="text-muted">Sep 20, 2026</small>
                                        </div>
                                        <button class="btn btn-sm btn-secondary py-0 px-2 text-nowrap" style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#announcementModal"
                                                data-title="New IT Procurement Guidelines"
                                                data-date="Sep 20, 2026"
                                                data-content="Updated policies for software license requests and hardware requisitions are now active. Please review the updated catalog before creating new PRE requests."><i class="ti ti-eye"></i></button>
                                    </div>

                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                        <div class="text-truncate me-2" style="max-width: 190px;">
                                            <span class="d-block text-dark fw-medium small text-truncate">Updated Vendor Selection Form</span>
                                            <small class="text-muted">Sep 22, 2026</small>
                                        </div>
                                        <button class="btn btn-sm btn-secondary py-0 px-2 text-nowrap" style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#announcementModal"
                                                data-title="Updated Vendor Selection Form"
                                                data-date="Sep 22, 2026"
                                                data-content="A revised vendor evaluation form (Form V-2) is required for all purchases exceeding $5,000. Download the template from the documents tab."><i class="ti ti-eye"></i></button>
                                    </div>

                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                        <div class="text-truncate me-2" style="max-width: 190px;">
                                            <span class="d-block text-dark fw-medium small text-truncate">Year-End Inventory Audit</span>
                                            <small class="text-muted">Oct 01, 2026</small>
                                        </div>
                                        <button class="btn btn-sm btn-secondary py-0 px-2 text-nowrap" style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#announcementModal"
                                                data-title="Year-End Inventory Audit"
                                                data-date="Oct 01, 2026"
                                                data-content="Annual inventory checks will begin on October 1st. Department heads are requested to finalize all pending delivery receipts and PO verifications."><i class="ti ti-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Announcement Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="modalDate"><i class="ti ti-calendar me-1"></i> Date</p>
                    <div id="modalContent" class="text-dark">
                        Announcement details...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            row-gap: 6px;
        }
        .calendar-days div {
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .calendar-days div:hover:not(.day-active) {
            background-color: #f1f5f9;
        }
        .calendar-days .day-active {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
        }
        .calendar-days .day-event {
            border: 1px solid #65ac86;
            color: #65ac86;
            font-weight: 600;
        }
    </style>

    <script>
        window.prChartMonths = @json($chartMonths);
        window.prChartPending = @json($chartPending);
        window.prChartApproved = @json($chartApproved);
    </script>


@endsection
