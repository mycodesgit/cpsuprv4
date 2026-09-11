<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right"
    };
    $(document).ready(function() {
        $('#addItem').submit(function(event) {
            event.preventDefault();
            
            var formData = $(this).serialize();

            $.ajax({
                url: itemCreateRoute,
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        console.log(response);
                        $(document).trigger('itemAdded');
                        $('#addItem')[0].reset();
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

        var dataTable = $('#itemTable').DataTable({
            "ajax": {
                "url": itemReadRoute,
                "type": "GET",
            },
            destroy: true,
            info: true,
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            order: [[0, 'asc']],
            "columns": [
                {
                    data: 'item_description',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data.length > 100 ? data.substring(0, 100) + '...' : data;
                        }
                        return data;
                    }
                },
                {data: 'uname'},
                {
                    data: 'estimated_cost',
                    render: function(data, type, row) {
                        if (type === 'display' && data !== null) {
                            // Formats "32000.00" back to "32,000.00" for table view
                            return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        return data;
                    }
                },
                {data: 'cname'},
                {
                    data: 'id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var buttons = '<button type="button" class="btn btn-sm btn-success btn-itemedit mr-1 text-light" data-id="' + row.id + '" data-catename="' + row.category_id + '" data-unitname="' + row.unit_id + '" data-itemdesc="' + row.item_description + '" data-itemcost="' + row.estimated_cost + '" data-toggle="tooltip" data-placement="top" title="Edit Item.">';
                            buttons += '<i class="ti ti-pencil"></i> </button>'+'&nbsp;';
                            if (isAdmin || isProcurementOfficer || isChecker) {
                                buttons += '<button type="button" value="' + data + '" class="btn btn-sm btn-danger item-delete" data-toggle="tooltip" data-placement="top" title="Delete Item."><i class="ti ti-trash"></i> </button>';
                            }
                            return buttons;
                        } else {
                            return data;
                        }
                    },
                },
            ],
            "createdRow": function (row, data, index) {
                $(row).attr('id', 'tr-' + data.itid); 
            }
        });
        $(document).on('itemAdded', function() {
            dataTable.ajax.reload();
        });
        dataTable.on('draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });

    $(document).on('click', '.btn-itemedit', function() {
        var id = $(this).data('id');
        var catName = $(this).data('catename');
        var unitName = $(this).data('unitname');
        var itemdesc = $(this).data('itemdesc');
        var itemCost = $(this).data('itemcost');

        $('#editItemId').val(id);
        $('#editItemCategory').val(catName).trigger('change');
        $('#editItemUnit').val(unitName).trigger('change');
        $('#editItemDescripName').val(itemdesc);
        $('#editItemCost').val(itemCost);

        $('#editItemModal').modal('show');
    });

    $('#editItemForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: itemUpdateRoute,
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    $('#editItemModal').modal('hide');
                    $(document).trigger('itemAdded');
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