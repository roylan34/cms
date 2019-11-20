import {
    SHOW_FORM,
    CURRENT_SEARCH
} from '../actions/currentAction'


const initial_state = {
    isShowForm: false,
    formTitle: '',
    actionForm: '',
    id: '',
    search: {}
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
        case CURRENT_SEARCH:
            return {
                ...state,
                search: { ...action.search },
            }
            break;
        default:
            return state;
            break;
    }
}