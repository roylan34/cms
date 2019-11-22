import {
    SEARCH_TABLE,
} from '../actions/searchAction'


const initial_state = {
    search: {},
};

export default function searchReducer(state = initial_state, action) {

    switch (action.type) {
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