import {
    SHOW_FORM_CHANGEPASS
} from '../actions/userDetailsAction'


const initial_state = {
    isShowForm: false
};

export default function changePassReducer(state = initial_state, action) {

    switch (action.type) {
        case SHOW_FORM_CHANGEPASS:
            return {
                ...state,
                isShowForm: !state.isShowForm
            }
            break;
        default:
            return state;
            break;
    }
}