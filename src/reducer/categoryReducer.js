import {
    SHOW_FORM_CATEGORY
} from '../actions/categoryAction'


const initial_state = {
    isShowForm: false,
    formTitle: '',
    actionForm: '',
    id: '',
};

export default function categoryReducer(state = initial_state, action) {

    switch (action.type) {
        case SHOW_FORM_CATEGORY:
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