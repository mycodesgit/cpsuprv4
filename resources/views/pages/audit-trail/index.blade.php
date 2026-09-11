@extends('layout.app')

@section('title')
    CPSU PR V.4 | Audit Logs
@endsection

@section('body')

    <style>
        .json-container {
            background-color: #000000;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.875rem;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 450px;
            overflow-y: auto;
        }

        /* JSON Syntax Coloring */
        .json-key { color: #9cdcfe; font-weight: bold; }       /* Light Blue */
        .json-string { color: #ce9178; }                    /* Soft Red / Orange */
        .json-number { color: #b5cea8; }                    /* Light Green */
        .json-boolean { color: #569cd6; font-weight: bold; }/* Blue */
        .json-null { color: #569cd6; font-weight: bold; }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <!-- Dashboard Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Audit Logs Management</h1>
                        <p class="text-muted small mb-0">View and manage audit logs for all system activities.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary">
                            Export Report
                        </button>
                    </div>
                </div>

                <!-- Top Row: Metric Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <ul class="nav nav-pills mb-3 bg-light p-2 rounded-2 d-inline-flex col-md-12" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-one-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-one" type="button" role="tab"
                                    aria-controls="pills-one" aria-selected="true">
                                    User Audit Logs
                                </button>
                            </li>
                            &nbsp;
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-two-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-two" type="button" role="tab"
                                    aria-controls="pills-two" aria-selected="false" tabindex="-1">
                                    Category Audit Logs
                                </button>
                            </li>
                            &nbsp;
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-three-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-three" type="button" role="tab"
                                    aria-controls="pills-three" aria-selected="false" tabindex="-1">
                                    Unit Audit Logs
                                </button>
                            </li>
                            &nbsp;
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-four-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-four" type="button" role="tab"
                                    aria-controls="pills-four" aria-selected="false" tabindex="-1">
                                    Item Audit Logs
                                </button>
                            </li>
                            &nbsp;
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-five-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-five" type="button" role="tab"
                                    aria-controls="pills-five" aria-selected="false" tabindex="-1">
                                    Office Audit Logs
                                </button>
                            </li>
                            &nbsp;
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-six-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-six" type="button" role="tab"
                                    aria-controls="pills-six" aria-selected="false" tabindex="-1">
                                    Year Audit Logs
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab" tabindex="0">
                                <div class="card card-animate">
                                    <div class="card-header pt-3">
                                        <h6 class="card-title">
                                            <i class="ti ti-users"></i> User Audit Logs
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive mt-2 p-2">
                                            <table id="userauditTable" class="table table-hover styled-table" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                        <th>IP Address</th>
                                                        <th>Browser & OS</th>
                                                        <th>Date & Time</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-two" role="tabpanel" aria-labelledby="pills-two-tab" tabindex="0">
                                <div class="card card-animate">
                                    <div class="card-header pt-3">
                                        <h6 class="card-title">
                                            <i class="ti ti-box"></i> Category Audit Logs
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive mt-2 p-2">
                                            <table id="categoryauditTable" class="table table-hover styled-table" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                        <th>IP Address</th>
                                                        <th>Browser & OS</th>
                                                        <th>Date & Time</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-three" role="tabpanel" aria-labelledby="pills-three-tab" tabindex="0">
                                <div class="card card-animate">
                                    <div class="card-header pt-3">
                                        <h6 class="card-title">
                                            <i class="ti ti-box"></i> Units Audit Logs
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive mt-2 p-2">
                                            <table id="unitauditTable" class="table table-hover styled-table" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                        <th>IP Address</th>
                                                        <th>Browser & OS</th>
                                                        <th>Date & Time</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-four" role="tabpanel" aria-labelledby="pills-four-tab" tabindex="0">
                                <div class="card card-animate">
                                    <div class="card-header pt-3">
                                        <h6 class="card-title">
                                            <i class="ti ti-box"></i> Items Audit Logs
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive mt-2 p-2">
                                            <table id="itemauditTable" class="table table-hover styled-table" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                        <th>IP Address</th>
                                                        <th>Browser & OS</th>
                                                        <th>Date & Time</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-five" role="tabpanel" aria-labelledby="pills-five-tab" tabindex="0">
                                <div class="card card-animate">
                                    <div class="card-header pt-3">
                                        <h6 class="card-title">
                                            <i class="ti ti-box"></i> Office Audit Logs
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive mt-2 p-2">
                                            <table id="officeauditTable" class="table table-hover styled-table" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                        <th>IP Address</th>
                                                        <th>Browser & OS</th>
                                                        <th>Date & Time</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-six" role="tabpanel" aria-labelledby="pills-six-tab" tabindex="0">
                                <div class="card card-animate">
                                    <div class="card-header pt-3">
                                        <h6 class="card-title">
                                            <i class="ti ti-box"></i> Year Audit Logs
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive mt-2 p-2">
                                            <table id="yearauditTable" class="table table-hover styled-table" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Action</th>
                                                        <th>IP Address</th>
                                                        <th>Browser & OS</th>
                                                        <th>Date & Time</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal to View Formatted JSON Payload -->
    <div class="modal fade" id="auditUserDetailsModal" tabindex="-1" aria-labelledby="auditUserDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditUserDetailsModalLabel">Action Data Payload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jsonPayloadDisplay" class="json-container"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="auditCategoryDetailsModal" tabindex="-1" aria-labelledby="auditCategoryDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditCategoryDetailsModalLabel">Action Data Payload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jsonPayloadDisplayCategory" class="json-container"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="auditunitDetailsModal" tabindex="-1" aria-labelledby="auditunitDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditunitDetailsModalLabel">Action Data Payload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jsonPayloadDisplayUnit" class="json-container"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="auditItemDetailsModal" tabindex="-1" aria-labelledby="auditItemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditItemDetailsModalLabel">Action Data Payload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jsonPayloadDisplayItem" class="json-container"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="auditOfficeDetailsModal" tabindex="-1" aria-labelledby="auditOfficeDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditOfficeDetailsModalLabel">Action Data Payload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jsonPayloadDisplayOffice" class="json-container"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="auditYearDetailsModal" tabindex="-1" aria-labelledby="auditYearDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditYearDetailsModalLabel">Action Data Payload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jsonPayloadDisplayYear" class="json-container"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var userAuditReadRoute = "{{ route('audit-trail.show.user') }}";
        var categoryAuditReadRoute = "{{ route('audit-trail.show.category') }}";
        var unitAuditReadRoute = "{{ route('audit-trail.show.unit') }}";
        var itemAuditReadRoute = "{{ route('audit-trail.show.item') }}";
        var officeAuditReadRoute = "{{ route('audit-trail.show.office') }}";
        var yearAuditReadRoute = "{{ route('audit-trail.show.year') }}";
    </script>
@endsection