import React, { useEffect } from 'react';
import DataTable from '../../helpers/table';

export default function CurrentContract(props) {

    let dtInstance = null;

    useEffect(() => {
    });


    return (
        <div>
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
                    "Created at"
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