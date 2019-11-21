import React, { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import DataTable from '../helpers/table';
import { Modal } from 'antd';

export default function ActivityLogs() {

    const state_logs = useSelector(state => state.activityLogsReducer);
    const dispatch = useDispatch();

    useEffect(() => {
        console.log('rerender logs');
        // return () => (null);
    }, [state_logs.selected_row_id]);

    return (
        <Modal
            title="ACTIVITY LOGS"
            visible={state_logs.isShowLogs}
            onCancel={() => dispatch({ type: 'SHOW_ACTIVITY_LOGS', id: '' })}
            footer={null}
        >
            {
                state_logs.selected_row_id ?
                    <DataTable
                        id="dtActivityLogs"
                        url="logs.php"
                        param={d => {
                            delete d.columns; //Remove built-in paramaters.
                            d.id = state_logs.selected_row_id;
                        }}
                        serverSide={false}
                        dom="flrtip"
                        headers={[
                            "#",
                            "Category",
                            "Action",
                            "Modified at"
                        ]}
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
                                    return data.category;
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row, meta) {
                                    return data.action;
                                }
                            },
                            {
                                data: null,
                                render: function (data) {
                                    return data.created_at;
                                }
                            },
                        ]}
                    >
                    </DataTable>
                    : null
            }

        </Modal>
    )


}