import { SHOW_ACTIVITY_LOGS, GET_ROW_ID } from '../actions/activityLogsAction';


const initial_state = {
    selected_row_id: '',
    isShowLogs: false
}

export default function activityLogs(state = initial_state, action) {

    switch (action.type) {
        case SHOW_ACTIVITY_LOGS:

            return {
                ...state,
                isShowLogs: !state.isShowLogs,
                selected_row_id: action.id
            }
            break;
        case GET_ROW_ID:
            return {
                ...state,
                selected_row_id: action.id
            }
        default:
            return state;
            break;
    }
}