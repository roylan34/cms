import { FETCH_ALL_CATEGORIES } from './../actions/categoriesAction';


const initial_state = {
    categories: []
};

export default function categoriesReducer(state = initial_state, action) {
    switch (action.type) {
        case FETCH_ALL_CATEGORIES:
            return {
                ...state,
                categories: action.payload
            }
            break;
        default:
            return state;
            break;
    }
}