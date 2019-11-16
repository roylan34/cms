import {
    SHOW_FORM
} from '../actions/currentAction'


const initial_state = {
    isShowForm: false,
    formTitle: '',
    actionForm: '',
    id: ''
};

export default function currentReducer(state = initial_state, action) {

    switch (action.type) {
        case SHOW_FORM:
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