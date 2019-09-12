import React from 'react';
import { Redirect } from "react-router-dom";
import Cookies from "../helpers/cookies";

class Auth {

    /**
     *  Validate user if one of the modules has access then proceed to dashboard.
     */
    static isAuthenticated(restProps) {
        const app_module = JSON.parse(Cookies.get('app_module')) || null;
        const user_role = Cookies.get('user_role');
        const blocklist_url = ['/account/company', '/account/manager', '/settings', '/inventory/settings', '/mrf/settings']; //Block list url if account type is a User.
        const blocked_url = blocklist_url.filter(function (url) {
            return url == restProps.path;
        });
        if (app_module) {
            const { app_mif, app_invnt, app_mrf, app_pm } = app_module;
            if (restProps.module == 'mif' && app_mif == 0) {
                return false;
            }
            if (restProps.module == 'inventory' && app_invnt == 0) {
                return false;
            }
            if (restProps.module == 'mrf' && app_mrf == 0) {
                return false;
            }
            if (restProps.module == 'pm' && app_pm == 0) {
                return false;
            }
            //Validating account role if User, cant access in blocklist uri.
            if (blocked_url.length > 0 && user_role == 'User') {
                return false;
            }

            return true;
        }
        else {
            return false;
        }
    }
    /**
     * Redirect the user to first available module has access.
     */
    static redirectModule() {
        const app_module = JSON.parse(Cookies.get('app_module')) || null;
        if (app_module) {
            const { app_mif, app_invnt, app_mrf, app_pm } = app_module;
            if (app_mif == 1)
                return <Redirect to="/mif/current" />;
            else if (app_invnt == 1)
                return <Redirect to="/inventory/current" />;
            else if (app_mrf == 1)
                return <Redirect to="/mrf/current" />;
            else if (app_pm == 1)
                return <Redirect to="/pm/current" />;
            else
                return <Redirect to="/login" />;
        }
        else {
            return <Redirect to="/login" />
        }
    }
}

export default Auth;