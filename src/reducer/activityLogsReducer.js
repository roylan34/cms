import { SHOW_ACTIVITY_LOGS } from '../actions/activityLogsAction';


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
        default:
            return state;
            break;
    }
}