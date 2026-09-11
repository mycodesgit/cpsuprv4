<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right"
    };
    $(document).ready(function() {
        $('#addUnit').submit(function(event) {
            event.preventDefault();

            var unitName = $('input[name="unit_name"]').val();
            if (!unitName.trim()) { 
                toastr.error("Unit name is required");
                return;  
            }
            
            var formData = $(this).serialize();

            $.ajax({
                url: unitCreateRoute,
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        console.log(response);
                        $(document).trigger('unitAdded');
                        $('#addUnitModal').modal('hide');
                        $('input[name="unit_name"]').val('');
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

        var dataTable = $('#unitTable').DataTable({
            "ajax": {
                "url": unitReadRoute,
                "type": "GET",
            },
            destroy: true,
            info: true,
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            "columns": [
                {data: 'unit_name'},
                {
                    data: null,
                    render: function (data, type, row) {
                        let status = '';

                        if (row.status == 1) {
                            status = '<span class="badge bg-success-subtle text-success">Available</span>';
                        } else {
                            status = '<span class="badge bg-danger-subtle text-danger">Deleted</span>';
                        } 
                        return status;
                    }
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var buttons = '<button type="button" class="btn btn-sm btn-success btn-unitedit mr-1 text-light" data-id="' + row.id + '" data-unitname="' + row.unit_name + '" data-toggle="tooltip" data-placement="top" title="Edit Unit.">';
                            buttons += '<i class="ti ti-pencil"></i> </button>'+'&nbsp;';
                            if (isAdmin || isProcurementOfficer || isChecker) {
                                buttons += '<button type="button" value="' + data + '" class="btn btn-sm btn-danger unit-delete" data-toggle="tooltip" data-placement="top" title="Delete Unit."><i class="ti ti-trash"></i> </button>';
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
        $(document).on('unitAdded', function() {
            dataTable.ajax.reload();
        });
        dataTable.on('draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });

    $(document).on('click', '.btn-unitedit', function() {
        var id = $(this).data('id');
        var unitName = $(this).data('unitname');
        $('#editUnitId').val(id);
        $('#editUnitName').val(unitName);
        $('#editUnitModal').modal('show');
    });

    $('#editUnitForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: unitUpdateRoute,
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    $('#editUnitModal').modal('hide');
                    $(document).trigger('unitAdded');
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
</script>