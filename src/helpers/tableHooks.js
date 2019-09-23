import React, { useEffect } from 'react';
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

export default function DataTable(props) {

    let dtInstance = null;
    const dtRef = React.createRef();
    /**
    * Initialize DataTable with props.
    */
    function dataTable() {
        dtInstance = $(dtRef.current).DataTable({
            "dom": props.dom,
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
            "stateSave": true,  //save the pagination #, ordering, show records # and etc
            "ordering": false,
            "ajax": {
                "url": `${API_URL}/${props.url}`,
                "type": "POST",
                "dataSrc": 'records',
                "data": props.param
            },
            "buttons": props.buttons,
            "columns": props.columns,
            "columnDefs": props.columnDefs,
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
            "footerCallback": props.footerCb
        });
    }
    /**
     * Build tH element in each column.
     */
    function TableHeaders() {

        return (
            <thead>
                {
                    props.headers.map(header => {
                        return <th key={(isEmpty(header) ? makeId() : header)} className="text-left">
                            {header}
                        </th>
                    })
                }
            </thead>
        )
    }
    /**
     * Cancel all ajax request in-progress if component is WillUnmount.
     */
    function abortDataTable() {
        if (typeof $ !== 'undefined' && $.fn.dataTable) {
            for (let i = 0; i < $.fn.dataTable.settings.length; i++) {
                $.fn.dataTable.settings[i].jqXHR.abort();
            }
        }
    }

    function __componentWillUnMount() {
        props.onRef(null);
        abortDataTable();
        $('.dtCurrentContract_wrapper').find('table').DataTable().destroy(true);
    }

    useEffect(() => {
        dataTable();
        props.onRef(dtInstance); //Get reference of this functional component.
        return __componentWillUnMount;
    })

    return (
        <div id="dtContainer">
            <table className="table table-condensed table-striped table-hover" ref={dtRef} id={props.id}>
                <TableHeaders />
                {props.headerSearch}
            </table>
        </div >
    );
}

//TypeChecking Props
DataTable.propTypes = {
    id: PropTypes.string.isRequired,
    url: PropTypes.string.isRequired,
    dom: PropTypes.string,
    buttons: PropTypes.arrayOf(PropTypes.object),
    columns: PropTypes.arrayOf(PropTypes.object).isRequired,
    columnDefs: PropTypes.arrayOf(PropTypes.object),
    footerCb: PropTypes.func,
    param: PropTypes.oneOfType([
        PropTypes.object,
        PropTypes.func
    ])
}

//Default Props
DataTable.defaultProps = {
    columnDefs: []
}