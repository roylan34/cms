import {
    SEARCH_TABLE,
    RESET_SEARCH_TABLE
} from '../actions/searchAction'


const initial_state = {
    search: {},
};

export default function searchReducer(state = initial_state, action) {

    switch (action.type) {
        case RESET_SEARCH_TABLE:
            return initial_state;
            break;
        case SEARCH_TABLE:
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