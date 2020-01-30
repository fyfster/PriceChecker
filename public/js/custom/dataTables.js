var TableDatatablesAjax = function () {
    var handleList = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },
            loadingMessage: dataTableLoading + '...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                // save datatable state(pagination, sort, etc) in cookie.
                "bStateSave": true,

                "searchable": true,

                // save custom filters to the state
                "fnStateSaveParams":    function ( oSettings, sValue ) {
                    $("#datatable_ajax tr.filter .form-control").each(function() {
                        sValue[$(this).attr('name')] = $(this).val();
                    });

                    return sValue;
                },

                // read the custom filters from saved state and populate the filter inputs
                "fnStateLoadParams" : function ( oSettings, oData ) {
                    //Load custom filters
                    $("#datatable_ajax tr.filter .form-control").each(function() {
                        var element = $(this);
                        if (oData[element.attr('name')]) {
                            element.val( oData[element.attr('name')] );
                        }
                    });

                    return true;
                },
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 20, // default record count per page
                "ajax": { // define ajax settings
                    "url": $('.table-route').data('url'), // ajax URL
                    "type": "POST", // request type
                },
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "order": orderColumn,// set first column as a default sort by asc,
                "columnDefs": columnDefs,//define the columns that are not sortable,

                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": dataTableEmpty,
                    "info": dataTableInfo,
                    "infoEmpty": dataTableNoEntries,
                    "infoFiltered": dataTableFiltered,
                    "lengthMenu": "<span class='seperator'>|</span>" + dataTableEntries + "<span class='seperator'>|</span>",
                    "search": dataTableSearch + ":",
                    "zeroRecords": dataTableNoRecords,
                    "paginate": {
                        "page": dataTablePage,
                        "pageOf": dataTableOf
                    }
                },
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            grid.setAjaxParam("searchFilter", $('input.form-filter[name="search"]').val());
            grid.getDataTable().ajax.reload();
        });

        // handle group action clear button click
        grid.getTableWrapper().on('click', '.table-group-action-clear', function (e) {
            if ($('#table-actions-custom').length > 0) {
                $('#table-actions-custom').find('input').each(function(i) {
                    $(this).val('');
                    grid.setAjaxParam("searchFilter["+$(this).prop('name')+"]", $(this).val());
                    if ($(this).prop('checked') == true) {
                        $(this).removeProp('checked');
                    }
                });
            }
            if ($('.table-group-action-input').length > 0) {
                $('.table-group-action-input').val("");
                grid.setAjaxParam("searchFilter", $('input.form-filter[name="search"]').val());
            }
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            grid.getDataTable().ajax.reload();
        });
    }

    return {

        //main function to initiate the module
        init: function () {
            handleList();
        }

    };

}();

jQuery(document).ready(function() {
    TableDatatablesAjax.init();
});