import React from 'react';
import { Redirect } from "react-router-dom";
import Cookies from "../helpers/cookies";
import Jwt from "../helpers/jwt";
import { isEmpty } from './utils';

class Auth {

    constructor() {
        this.app_token = null;
        this.sid = null;
    }

    /**
     *  Validate user if one of the modules has access then proceed to dashboard.
     */
    static isAuthenticated(restProps) {
        this.app_token = Cookies.get('token');
        this.sid = Cookies.get('sid');
        const blocklist_url = ['/account', '/settings']; //Block list url if account type is a User.
        const blocked_url = blocklist_url.filter(function (url) {
            return url == restProps.location.pathname;
        });
        if (!isEmpty(this.app_token) && !isEmpty(this.sid)) {

            const user_role = Jwt.get('user_role');
            //Not allowed to access the account has role USER.
            if (blocked_url.length > 0 && user_role == 'USER') {
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    static logout() {
        if (!isEmpty(this.app_token) && !isEmpty(this.sid)) {
            Cookies.clear();
            //ajax
            //will clear stored session in the server.
        }
    }

}

export default Auth;