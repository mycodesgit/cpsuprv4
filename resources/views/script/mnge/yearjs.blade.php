<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right"
    };
    $(document).ready(function() {
        $('#addYear').submit(function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: yearCreateRoute,
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        console.log(response);
                        $(document).trigger('yearAdded');
                        $('#addYear')[0].reset();
                    } else {
                        toastr.error(response.message);
                        console.log(response);
                    }
                },
                error: function(xhr, status, error, message) {
                    var errorMessage = xhr.responseText ? JSON.parse(xhr.responseText).message : 'An error occurred';
                    toastr.error(errorMessage);
                }
            });
        });

        var dataTable = $('#yearTable').DataTable({
            "ajax": {
                "url": yearReadRoute,
                "type": "GET",
            },
            destroy: true,
            info: true,
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            "columns": [
                {data: 'pryear'},
                {data: 'status',
                        render: function(data, type, row) {
                        switch(parseInt(data)) {
                            case 1:
                                return '<span class="badge bg-info">Enabled</span>';
                            case 2:
                                return '<span class="badge bg-warning">Disabled</span>';
                            case 3:
                                return '<span class="badge bg-warning">Upcoming</span>';
                            default:
                                return '<span class="badge bg-secondary">Unknown Status</span>';
                        }
                    },
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var buttons = '<button type="button" class="btn btn-sm btn-success btn-yearedit mr-1 text-light" data-id="' + row.id + '" data-pryear="' + row.pryear + '" data-status="' + row.status + '" data-toggle="tooltip" data-placement="top" title="Edit Year.">';
                            buttons += '<i class="ti ti-pencil"></i> </button>'+'&nbsp;';
                            if (isAdmin || isProcurementOfficer || isChecker) {
                                buttons += '<button type="button" value="' + data + '" class="btn btn-sm btn-danger year-delete" data-toggle="tooltip" data-placement="top" title="Delete Year."><i class="ti ti-trash"></i> </button>';
                            }
                            return buttons;
                        } else {
                            return data;
                        }
                    },
                },
            ],
            "createdRow": function (row, data, index) {
                $(row).attr('id', 'tr-' + data.id); 
            }
        });
        $(document).on('yearAdded', function() {
            dataTable.ajax.reload();
        });
        dataTable.on('draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });

    $(document).on('click', '.btn-yearedit', function() {
        var id = $(this).data('id');
        var prYear = $(this).data('pryear');
        var prStatus = $(this).data('status');

        $('#editYearId').val(id);
        $('#editYearName').val(prYear);
        $('#editYearStatus').val(prStatus);
        $('#editYearModal').modal('show');
    });

    $('#editYearForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: yearUpdateRoute,
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    $('#editYearModal').modal('hide');
                    $(document).trigger('yearAdded');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error, message) {
                var errorMessage = xhr.responseText ? JSON.parse(xhr.responseText).message : 'An error occurred';
                toastr.error(errorMessage);
            }
        });
    });

    $(function () {
        $('#addYear').validate({
            rules: {
                pryear: {
                    required: true,
                },
            },
            messages: {
                pryear: {
                    required: "Please Enter Year",
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.col-md-12').append(error);        
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>