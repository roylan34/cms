import {
    LOGIN_USER_DETAILS
} from '../actions/userDetailsAction'
import Jwt from '../helpers/jwt';

const initial_state = {
    fullname: '',
    user_role: '',
    email: ''
};

export default function userDetailsReducer(state = initial_state, action) {

    switch (action.type) {
        case LOGIN_USER_DETAILS:
            return {
                ...state,
                fullname: Jwt.get('firstname') + ' ' + Jwt.get('lastname'),
                user_role: Jwt.get('user_role'),
                email: 'testemail',
            }
            break;
        default:
            return state;
            break;
    }
}