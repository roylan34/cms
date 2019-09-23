import React from 'react';
import PropTypes from 'prop-types';
import { API_URL } from "./constant";
import { isEmpty, makeId } from './utils';
import $ from 'jquery';


import 'datatables.net-bs';
import jsZip from 'jszip';
import 'datatables.net-buttons-bs';
import 'datatables.net-responsive-bs';
import 'datatables.net-buttons/js/dataTables.buttons.min';
import 'datatables.net-buttons/js/buttons.html5.min'; //HTML5 export 
import 'datatables.net-buttons/js/buttons.print.min'; //Print button

// This line was the one missing
window.JSZip = jsZip;

class Table extends React.Component {

    constructor(props) {
        super(props);

        this.dtInstance = null;
        this.reloadDataTable = this.reloadDataTable.bind(this);

    }
    /**
     * Build tH element in each column.
     */
    buildHeaders() {
        return this.props.headers.map(header => {
            return <th key={(isEmpty(header) ? makeId() : header)} className="text-left">
                {header}
            </th>;
        });
    }
    /**
    * Initialize DataTable with props.
    */
    dataTable() {
        this.dtInstance = $(this.refs.dt).DataTable({
            "dom": this.props.dom,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "loadingRecords": "Please wait - loading..."
            },
            "bDestroy": true,
            "serverSide": true,
            "processing": true,
            "stateSave": true,                                          //save the pagination #, ordering, show records # and etc
            "ordering": false,
            "ajax": {
                "url": `${API_URL}/${this.props.url}`,
                "type": "POST",
                "dataSrc": 'records',
                "data": this.props.param
            },
            "buttons": this.props.buttons(this),
            "columns": this.props.columns,
            "columnDefs": this.props.columnDefs,
            "deferRender": true,
            "preDrawCallback": function (settings) {
                //Override default style of buttons.
                let buttons = document.querySelectorAll(".dt-buttons > button");
                if (buttons.length > 0) {
                    buttons.forEach(function (elem) {
                        elem.classList.remove('btn-default');
                        elem.classList.add('btn-primary', 'btn-flat', 'btn-sm');
                    })
                }
            },
            "footerCallback": this.props.footerCb
        });
    }
    /** Refresh table from Ajax source. */
    reloadDataTable() {
        this.dtInstance.ajax.reload(null, false);
    }
    /**
     * Cancel all ajax request in-progress if component is WillUnmount.
     */
    abortDataTable() {
        if (typeof $ !== 'undefined' && $.fn.dataTable) {
            for (let i = 0; i < $.fn.dataTable.settings.length; i++) {
                $.fn.dataTable.settings[i].jqXHR.abort();
            }
        }
    }
    componentDidMount() {
        this.props.onRef(this); //Get reference of this class.
        this.dataTable();
    }
    componentWillUnmount() {
        this.props.onRef(null)
        this.abortDataTable();
        $('.data-table-wrapper').find('table').DataTable().destroy(true);
    }
    render() {

        return (
            <div id="dtContainer">
                <table className="table table-condensed table-striped table-hover" ref="dt" id={this.props.id}>
                    {this.props.children}
                </table>
            </div >
        );

    }
}

//TypeChecking Props
Table.propTypes = {
    id: PropTypes.string.isRequired,
    url: PropTypes.string.isRequired,
    dom: PropTypes.string,
    buttons: PropTypes.func,
    columns: PropTypes.arrayOf(PropTypes.object).isRequired,
    columnDefs: PropTypes.arrayOf(PropTypes.object),
    footerCb: PropTypes.func,
    param: PropTypes.oneOfType([
        PropTypes.object,
        PropTypes.func
    ])
}

//Default Props
Table.defaultProps = {
    columnDefs: []
}

export default Table;


