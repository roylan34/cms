import {
    FETCH_LIST_COMP,
    FETCH_COMP_NAME,
    HALT_FETCHING_COMP
} from './../actions/drpAction';


const initial_state = {
    comp: { isFetching: false, data: [] }
};

export default function drpReducer(state = initial_state, action) {
    switch (action.type) {
        case FETCH_LIST_COMP:
            return {
                ...state,
                comp: { isFetching: action.isFetching, data: action.payload }
            }
            break;
        case HALT_FETCHING_COMP:
            return {
                ...state,
                comp: { isFetching: false, data: state.comp.data }
            }
            break;
        case FETCH_COMP_NAME:
            return {
                ...state,
                comp: { isFetching: false, data: action.payload }
            }
            break;
        default:
            return state;
            break;
    }
}