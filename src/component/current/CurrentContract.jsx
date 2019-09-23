import React, { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import DataTable from '../../helpers/table';
import HeaderSearch from './CurrentSearch';
import Form from './Form';

export default function CurrentContract(props) {

    let dtInstance = null;
    const dispatch = useDispatch();

    useEffect(() => {
        console.log('render');
    })
    return (
        <div>
            <Form />
            <DataTable
                id="dtCurrentContract"
                url="get_current.php"
                param={d => {
                    delete d.columns; //Remove built-in paramaters.
                }}
                onRef={ref => (dtInstance = ref)}
                dom="Blrtip"
                headers={[
                    "#",
                    "Company Name",
                    "Category",
                    "Date signed",
                    "Expiration",
                    "Status",
                    "Updated at",
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
                            return data.company_id;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.category;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.date_signed;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.valid_from.concat(' to ', data.valid_to);
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            var status = data.status;
                            var badge = "";
                            if (status === "ACTIVE") {
                                badge = "green";
                            } else {
                                badge = "red";
                            }
                            return '<span class="badge badge-' + badge + '">' + data.status + '</span>';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.updated_at;
                        }
                    }

                ]}
                buttons={[
                    {
                        text: "Refresh",
                        className: "",
                        action: function () {
                            dtInstance.ajax.reload(null, false)
                        }
                    },
                    {
                        text: "+ Add Contract",
                        className: "",
                        action: function () {
                            dispatch({ type: 'SHOW_FORM', formTitle: 'Add Contracts Details' })
                        }
                    },
                    {
                        text: "Open Search",
                        className: "",
                        action: function () {
                            dispatch({ type: 'SHOW_FORM', formTitle: 'Add Contracts Details' })
                        }
                    },
                ]}
            >
            </DataTable>
        </div>
    );
}