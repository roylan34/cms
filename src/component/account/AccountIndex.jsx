import React, { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import DataTable from '../../helpers/table';
import AccountForm from './AccountForm';


function Account(props) {

    let dtInstance = null;
    const dispatch = useDispatch();

    useEffect(() => {
        //Attached and delegate event to tbody
        const tbl = document.querySelectorAll("table#dtAccounts tbody");
        tbl[0].addEventListener('click', function (e) {
            e.preventDefault();
            const target = e.target;
            const _class = target.classList;
            const childElem = target.parentNode;
            let id_attr = target.getAttribute('data-id');

            if (_class.contains('viewAccount') || childElem.classList.contains('viewAccount')) {
                e.stopPropagation();
                id_attr = id_attr || childElem.getAttribute('data-id'); //If the elem is a sibling from its target node will lift up to its parent node.
                dispatch({ type: 'SHOW_FORM_ACCOUNT', id: id_attr, actionForm: 'edit', formTitle: 'Edit Account' });
            }
        });

    }, []);

    function handleRefreshTable() {
        dtInstance.ajax.reload(null, false);
    }
    return (
        <div className="account">
            <AccountForm refreshTable={handleRefreshTable} />
            <DataTable
                id="dtAccounts"
                url="get_accounts.php"
                param={d => {
                    delete d.columns; //Remove built-in paramaters.
                    d.action = "all";
                }}
                onRef={ref => (dtInstance = ref)}
                dom="Bflrtip"
                headers={[
                    "#",
                    "Username",
                    "Fullname",
                    "Email",
                    "Status",
                    "Role",
                    "Created at",
                    ""
                ]}
                serverSide={false}
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
                            return data.username;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.fullname;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.email;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            const status = data.status;
                            let badge = "";
                            if (status === "ACTIVE") {
                                badge = "green";
                            }
                            else if (status === "INACTIVE") {
                                badge = "red";
                            }
                            return `<span class="badge badge-${badge}">${status}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.user_role;
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
                            return `<a href="#" data-id=${data.id} class="btn btn-sm btn-success viewAccount"><i class="fa fa-pencil-square" aria-hidden="true"></i> EDIT</a>`;
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
                        text: '+ Add Account',
                        action: function () {
                            dispatch({ type: "SHOW_FORM_ACCOUNT", actionForm: 'add', formTitle: 'Add Account' })
                        }
                    },
                ]}
            >
            </DataTable>
        </div>
    );
}

export default Account;