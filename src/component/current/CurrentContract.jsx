import React, { useEffect } from 'react';
import { Modal } from 'antd';
import { useDispatch } from 'react-redux';
import DataTable from '../../helpers/table';
import HeaderSearch from './CurrentSearch';
import FormCurrent from './Form';
import CurrentServices from '../../services/currentServices.js';

export default function CurrentContract(props) {

    let dtInstance = null;
    const dispatch = useDispatch();

    useEffect(() => {
        //Attached and delegate event to tbody
        const tbl = document.querySelectorAll("table#dtCurrentContract tbody");
        tbl[0].addEventListener('click', function (e) {
            e.preventDefault();
            const target = e.target;
            const _class = target.classList;
            const id_attr = target.getAttribute('data-id');
            if (_class.contains('btnEditContract')) {
                e.stopPropagation();
                dispatch({ type: 'SHOW_FORM', actionForm: 'edit', formTitle: 'Edit Contracts Details', id: id_attr })
            }
            else if (_class.contains('btnRenewContract')) {
                e.stopPropagation();
                dispatch({ type: 'SHOW_FORM', actionForm: 'renew', formTitle: 'Renew Contracts Details', id: id_attr })
            }
            else if (_class.contains('btnCancelContract')) {
                Modal.confirm({
                    title: 'Confirmation',
                    content: 'Are you sure you want to CANCEL CONTRACT?',
                    onOk() { CurrentServices.updateStatus(id_attr, 'CANCEL') },
                    onCancel() { console.log('cancel') }
                });
            }
            else if (_class.contains('btnCloseContract')) {
                Modal.confirm({
                    title: 'Confirmation',
                    content: 'Are you sure you want to CLOSE CONTRACT?',
                    onOk() { CurrentServices.updateStatus(id_attr, 'CLOSED') },
                    onCancel() { console.log('cancel') }
                });
            }
        });

    })


    return (
        <div>
            <FormCurrent />
            <DataTable
                id="dtCurrentContract"
                url="get_current.php"
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
                    "Updated at",
                    ""
                ]}
                headerSearch={<HeaderSearch />}
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
                            const notify = data.notify_status;
                            let badge = "";
                            let status = "";
                            if (notify === "ACTIVE") {
                                badge = "green";
                                // status = `<small>${data.status}</small>`;
                            }
                            else if (notify === "NOTIFYING") {
                                badge = "orange";
                                // status = `<small>${data.status}</small>`;
                            } else {
                                badge = "red";
                            }
                            return `<span class="badge badge-${badge}">${notify} <div>${status}</div></span>`;
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            return data.updated_at;
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            let action_elem = '';
                            const status = data.notify_status;
                            action_elem += `<div class="dropdown">
                                                <button class="btn btn-success dropdown-toggle btn-sm" type="button" data-toggle="dropdown">Actions
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu dropdown-menu-right">`;
                            if (status === "ACTIVE" || status === "NOTIFYING") {
                                action_elem += `<li><a href="#" class="btnEditContract" data-id=${data.id}><i class="fa fa-pencil-square" aria-hidden="true"></i>EDIT</a></li>`
                                    + `<li><a href="#" class="btnCancelContract" data-id=${data.id}><i class="fa fa-ban" aria-hidden="true"></i>CANCEL CONTRACT</a></li>`;
                            }
                            if (status === "EXPIRED") {
                                action_elem += ` <li><a href="#" class="btnRenewContract" data-id=${data.id}><i class="fa fa-repeat" aria-hidden="true"></i>RENEW</a></li>`
                                    + `<li><a href="#" class="btnCloseContract" data-id=${data.id}><i class="fa fa-times-circle" aria-hidden="true"></i>CLOSE CONTRACT</a></li>`;
                            }
                            action_elem += `<li><a href="#" class="viewLogs" data-id=${data.id}>ACTIVITY LOGS</a></li></ul></div>`;
                            return action_elem;
                        }
                    }


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
                        text: "+ Add Contract",
                        className: "",
                        action: function () {
                            dispatch({ type: 'SHOW_FORM', actionForm: 'add', formTitle: 'Add Contracts Details' })
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