@extends('layout.app')

@section('title')
    CPSU PR V.4 | Dashboard
@endsection

@section('body')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                
                <!-- Dashboard Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Purchase Request Management Dashboard</h1>
                        <p class="text-muted small mb-0">System metrics, departmental request analytics, and real-time activity logs.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-plus"></i> Create PAP's / PRE
                        </button>
                    </div>
                </div>              

                <!-- Middle Row: Analytics Chart & Right Side Widgets -->
                <div class="row g-3">
                    <!-- Monthly Submissions Bar Chart -->
                    <div class="col-lg-9">
                        <div class="card card-animate mb-3">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title fw-bold mb-1">Purchase Requests Submitted</h5>
                                        <p class="text-muted small mb-0">Monthly submission breakdown for current year</p>
                                    </div>
                                    <span class="badge bg-light text-dark border">Jan - Dec</span>
                                </div>
                                <div style="height: 200px;">
                                    <canvas id="prSubmissionsChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Row: Metric Cards -->
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

    <!-- Chart.js Script and Custom Styles -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. Chart Initialization ---
            const ctx = document.getElementById('prSubmissionsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Submitted PRs',
                        data: [65, 78, 90, 81, 95, 110, 105, 125, 115, 130, 100, 140],
                        backgroundColor: '#65ac86',
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            border: { dash: [4, 4] },
                            grid: { color: '#f1f5f9' },
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- 2. Dynamic Calendar Implementation ---
            let currentDate = new Date(2026, 8, 10); // Initialized to September 2026 based on dashboard data
            const today = new Date(2026, 8, 10); // Reference date for current day highlight

            // Sample events array (Format: YYYY-MM-DD)
            const sampleEvents = ['2026-09-16', '2026-09-20', '2026-10-01'];

            function renderCalendar(date) {
                const monthYearText = document.getElementById('calendarMonthYear');
                const calendarDays = document.getElementById('calendarDays');
                calendarDays.innerHTML = '';

                const year = date.getFullYear();
                const month = date.getMonth();

                // Format month header (e.g., September 2026)
                const monthNames = ["January", "February", "March", "April", "May", "June", 
                                    "July", "August", "September", "October", "November", "December"];
                monthYearText.innerText = `${monthNames[month]} ${year}`;

                // Get first day index of current month and total days
                const firstDayIndex = new Date(year, month, 1).getDay();
                const totalDays = new Date(year, month + 1, 0).getDate();
                const prevLastDay = new Date(year, month, 0).getDate();

                // 1. Previous Month's Trailing Days
                for (let x = firstDayIndex; x > 0; x--) {
                    const dayDiv = document.createElement('div');
                    dayDiv.classList.add('text-muted', 'opacity-25');
                    dayDiv.innerText = prevLastDay - x + 1;
                    calendarDays.appendChild(dayDiv);
                }

                // 2. Current Month Days
                for (let i = 1; i <= totalDays; i++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.innerText = i;

                    // Check if day is today
                    if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        dayDiv.classList.add('day-active');
                    }

                    // Check for sample events
                    const formattedMonth = String(month + 1).padStart(2, '0');
                    const formattedDay = String(i).padStart(2, '0');
                    const dateStr = `${year}-${formattedMonth}-${formattedDay}`;

                    if (sampleEvents.includes(dateStr) && !dayDiv.classList.contains('day-active')) {
                        dayDiv.classList.add('day-event');
                    }

                    calendarDays.appendChild(dayDiv);
                }

                // 3. Next Month's Leading Days (Pad remaining grid slots to maintain full rows)
                const totalGridSlots = calendarDays.children.length;
                const remainingSlots = (totalGridSlots % 7 === 0) ? 0 : 7 - (totalGridSlots % 7);
                for (let j = 1; j <= remainingSlots; j++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.classList.add('text-muted', 'opacity-25');
                    dayDiv.innerText = j;
                    calendarDays.appendChild(dayDiv);
                }
            }

            // Initial render
            renderCalendar(currentDate);

            // Previous Month Button Action
            document.getElementById('prevMonth').addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar(currentDate);
            });

            // Next Month Button Action
            document.getElementById('nextMonth').addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar(currentDate);
            });

            // --- 3. Announcement Modal Population ---
            const modalElement = document.getElementById('announcementModal');
            modalElement.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const title = button.getAttribute('data-title');
                const date = button.getAttribute('data-date');
                const content = button.getAttribute('data-content');

                document.getElementById('modalTitle').innerText = title;
                document.getElementById('modalDate').innerHTML = `<i class="ti ti-calendar me-1"></i> Posted on ${date}`;
                document.getElementById('modalContent').innerText = content;
            });
        });
    </script>
@endsection