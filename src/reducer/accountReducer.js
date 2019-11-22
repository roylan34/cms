import {
    SHOW_FORM_ACCOUNT
} from '../actions/accountAction'


const initial_state = {
    isShowForm: false,
    formTitle: '',
    actionForm: '',
    id: '',
};

export default function accountReducer(state = initial_state, action) {

    switch (action.type) {
        case SHOW_FORM_ACCOUNT:
            return {
                ...state,
                isShowForm: !state.isShowForm,
                formTitle: action.formTitle,
                actionForm: action.actionForm,
                id: action.id || ''
            }
            break;
        default:
            return state;
            break;
    }
}