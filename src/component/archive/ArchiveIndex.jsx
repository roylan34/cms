import React, { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import DataTable from '../../helpers/table';
import ActivityLogs from '../ActivityLogs';

export default function CurrentContract(props) {

    let dtInstance = null;
    const dispatch = useDispatch();

    useEffect(() => {
        //Attached and delegate event to tbody
        const tbl = document.querySelectorAll("table#dtArchiveContract tbody");
        tbl[0].addEventListener('click', function (e) {
            e.preventDefault();
            const target = e.target;
            const _class = target.classList;
            const childElem = target.parentNode;
            let id_attr = target.getAttribute('data-id');

            if (_class.contains('viewLogs') || childElem.classList.value == 'viewLogs') {
                e.stopPropagation();
                id_attr = id_attr || childElem.getAttribute('data-id'); //If the elem is a sibling from its target node will lift up to its parent node.
                dispatch({ type: 'SHOW_ACTIVITY_LOGS', id: id_attr });
            }
        });

    }, []);


    return (
        <div>
            <ActivityLogs />
            <DataTable
                id="dtArchiveContract"
                url="get_archive.php"
                param={d => {
                    delete d.columns; //Remove built-in paramaters.
                    d.action = "all";
                }}
                onRef={ref => (dtInstance = ref)}
                dom="Blrtip"
                headers={[
                    "#",
                    "Company Name",
                    "Category",
                    "Valid from",
                    "Valid to",
                    "Status",
                    "Created at",
                    ""
                ]}
                // headerSearch={<HeaderSearch />}
                serverSide={true}
                columns={[
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1; //DataTable autoId for sorting.
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.company_name;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.cat_name;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.valid_from;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.valid_to;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            const status = data.status;
                            let badge = "";
                            if (status === "CANCEL") {
                                badge = "orange";
                            }
                            else if (status === "CLOSED") {
                                badge = "red";
                            }
                            return `<span class="badge badge-${badge}">${status}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            return data.created_at;
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            return `<a href="#" title="Activity logs" data-id=${data.id} class="viewLogs"><i class="fa fa-history fa-lg" aria-hidden="true"></i></a>`;
                        }
                    },


                ]}
                buttons={[
                    {
                        text: '<i class="fa fa-refresh" aria-hidden="true"></i>',
                        titleAttr: "Refresh",
                        action: function () {
                            dtInstance.ajax.reload(null, false)
                        }
                    },
                    {
                        text: '<i class="fa fa-search" aria-hidden="true"></i> Open Search',
                        className: "",
                        action: function () {
                        }
                    },
                ]}
            >
            </DataTable>
        </div>
    );
}