import Cookies from "../helpers/cookies";
import Jwt from "../helpers/jwt";
import { isEmpty } from './utils';
import $ from 'jquery';
import { API_URL } from './constant';;

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
            //Not allowed to access the account has role USER.Then itwas
            if (blocked_url.length > 0 && user_role == 'USER') {
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    static logout(cbHistory) {
        if (!isEmpty(this.app_token) && !isEmpty(this.sid)) {
            this.app_token = null;
            this.sid = null;
            Cookies.clear('token');
            Cookies.clear('sid');
            Jwt.clear();
            cbHistory.push('/login');

            //ajax
            //will clear stored session in the server.
            $.ajax({
                url: `${API_URL}/login.php`,
                data: { action: 'logout' },
                dataType: 'json',
                method: "POST",
                success: (res) => {
                    if (res.status === "success") {
                        console.log('successfully logout');
                    }
                }
            });


        }
    }

}

export default Auth;
