import {
    SHOW_FORM
} from '../actions/currentAction'


const initial_state = {
    isShowForm: false,
    formTitle: ''
};

export default function currentReducer(state = initial_state, action) {

    switch (action.type) {
        case SHOW_FORM:
            return {
                ...state,
                isShowForm: !state.isShowForm,
                formTitle: action.formTitle
            }
            break;
        default:
            return state;
            break;
    }
}