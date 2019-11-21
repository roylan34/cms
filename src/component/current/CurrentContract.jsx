import React, { useEffect, useRef } from 'react';
import { Modal } from 'antd';
import { useDispatch, useSelector } from 'react-redux';
import DataTable from '../../helpers/table';
import HeaderSearch from './CurrentSearch';
import FormCurrent from './Form';
import ActivityLogs from './../ActivityLogs';
import CurrentServices from '../../services/currentServices.js';
import { momentObjToString } from '../../helpers/utils';
import { instanceOf } from 'prop-types';

export default function CurrentContract(props) {

    let dtInstance = null;
    let toggleSearch = useRef();
    const dispatch = useDispatch();
    const state_current = useSelector(state => state.currentReducer.search);

    useEffect(() => {
        //Attached and delegate event to tbody
        const tbl = document.querySelectorAll("table#dtCurrentContract tbody");
        tbl[0].addEventListener('click', function (e) {
            e.preventDefault();
            const target = e.target;
            const _class = target.classList;
            const id_attr = target.getAttribute('data-id');

            if (target instanceof HTMLAnchorElement) {
                e.stopPropagation();
            }

            if (_class.contains('btnEditContract')) {
                dispatch({ type: 'SHOW_FORM', actionForm: 'edit', formTitle: 'Edit Contracts Details', id: id_attr })
            }
            else if (_class.contains('btnRenewContract')) {
                dispatch({ type: 'SHOW_FORM', actionForm: 'renew', formTitle: 'Renew Contracts Details', id: id_attr })
            }
            else if (_class.contains('btnCancelContract')) {
                Modal.confirm({
                    title: 'Confirmation',
                    content: (<div>Are you sure you want to <strong>CANCEL CONTRACT?</strong> </div>),
                    onOk() { CurrentServices.updateStatus(id_attr, 'CANCEL') },
                });
            }
            else if (_class.contains('btnCloseContract')) {
                Modal.confirm({
                    title: 'Confirmation',
                    content: (<div>Are you sure you want to <strong>CLOSE CONTRACT?</strong> </div>),
                    onOk() { CurrentServices.updateStatus(id_attr, 'CLOSED') },
                });
            }
            else if (_class.contains('viewLogs')) {
                dispatch({ type: 'SHOW_ACTIVITY_LOGS', id: id_attr });
            }
        });

    }, []);

    return (
        <div>
            <FormCurrent />
            <ActivityLogs />
            <DataTable
                id="dtCurrentContract"
                url="get_current.php"
                param={d => {
                    delete d.columns; //Remove built-in paramaters.
                    d.action = "all";
                    d.comp = state_current.comp;
                    d.category = state_current.category;
                    d.valid_from = momentObjToString(state_current.valid_from);
                    d.valid_to = momentObjToString(state_current.valid_to);
                    d.status = state_current.status;
                }}
                serverSide={true}
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
                headerSearch={<HeaderSearch span="8" />}
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
                        text: '<i class="fa fa-search" aria-hidden="true"></i> Open Searchs',
                        className: "",
                        action: function (e, dt, node, config) {
                            if (toggleSearch.current) {
                                node[0].innerText = "Open Search";
                                toggleSearch.current = false;
                                document.querySelector('.search-transition-height').classList.remove('transition-height');
                            } else {
                                node[0].innerText = "Close Search";
                                toggleSearch.current = true;
                                document.querySelector('.search-transition-height').classList.add('transition-height');
                            }

                        }
                    },
                ]}
            >
            </DataTable>
        </div>
    );
}