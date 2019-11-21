import React, { useEffect, useRef } from 'react';
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

/**
 * Build tH element in each column.
 */
function TableHeaders(props) {
    return (
        <thead>
            <tr>
                {
                    props.headers.map(header => {
                        return <th key={(isEmpty(header) ? makeId() : header)} className="text-left">
                            {header}
                        </th>
                    })
                }
            </tr>
        </thead>
    )
}

export default function Table(props) {

    let dtInstance = null;
    let dtRef = useRef();
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
            "serverSide": props.serverSide,
            "processing": true,
            "stateSave": true,                                          //save the pagination #, ordering, show records # and etc
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
     * Cancel all ajax request in-progress if component is WillUnmount.
     */
    function abortDataTable() {
        if (typeof $ !== 'undefined' && $.fn.dataTable) {
            for (let i = 0; i < $.fn.dataTable.settings.length; i++) {
                $.fn.dataTable.settings[i].jqXHR.abort();
            }
        }
    }
    function __componentDidMount() {
        dataTable();
        props.onRef(dtInstance); //Get reference of this class.

    }
    function __componentWillUnmount() {
        props.onRef(null)
        abortDataTable();
        $('.data-table-wrapper').find('table').DataTable().destroy(true);
    }

    useEffect(() => {
        __componentDidMount();
        return __componentWillUnmount;
    });
    return (
        <div id="dtContainer">
            <table className="table table-condensed table-striped table-hover" ref={dtRef} id={props.id}>
                {props.headerSearch}
                <TableHeaders headers={props.headers} />

            </table>
        </div >
    );

}

//TypeChecking Props
Table.propTypes = {
    id: PropTypes.string.isRequired,
    url: PropTypes.string.isRequired,
    dom: PropTypes.string,
    serverSide: PropTypes.bool,
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
Table.defaultProps = {
    columnDefs: [],
    serverSide: false,
    onRef: () => { }
}



