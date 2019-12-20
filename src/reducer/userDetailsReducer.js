import {
    LOGIN_USER_DETAILS,
    LOGOUT_USER_DETAILS
} from '../actions/userDetailsAction'
import Jwt from '../helpers/jwt';

const initial_state = {
    id: '',
    fullname: '',
    user_role: '',
    email: '',
    expr_time_stamp: ''
};

export default function userDetailsReducer(state = initial_state, action) {

    switch (action.type) {
        case LOGIN_USER_DETAILS:
            return {
                ...state,
                id: Jwt.get('id'),
                fullname: Jwt.get('firstname') + ' ' + Jwt.get('lastname'),
                user_role: Jwt.get('user_role'),
                email: Jwt.get('user_role'),
                expr_time_stamp: Jwt.get('expr_timestamp')
            }
            break;
        case LOGOUT_USER_DETAILS:
            return initial_state;
            break;
        default:
            return state;
            break;
    }
}