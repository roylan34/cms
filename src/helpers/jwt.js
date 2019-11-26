import Cookies from './cookies';
import { API_URL } from './constant';
import $ from 'jquery';
import { Base64 } from 'js-base64';

class Jwt {

    constructor() {
        //Preserve state of cookies token.
        this.c_token = null;
    }

    static get(input) {
        //Get cookies.
        if (!this.c_token) {
            this.c_token = Cookies.get('token');
            //Check if token stored in cookies.
            if (!this.c_token) {
                return null;
            }
        }

        var token = this.c_token.split('.');
        var payload = Base64.decode(token[1]);
        var parsePayload = JSON.parse(payload);
        //Check token name if exist.
        if (!parsePayload.hasOwnProperty(input)) {
            throw new Error('Invalid token name');
        }
        return parsePayload[input];
    }

    static serverVerify() {
        var xhr = $.ajax({
            async: false,
            url: `${API_URL}/jwtInvokeVerify.php`,
        });

        if (xhr.status == "401") {
            alert('Access not allowed. \n Please contact the developer to assist you. Thanks!');
            return false;
        }
        return true;
    }

    static clear() {
        this.c_token = null;
        return this.c_token;
    }

}

export default Jwt;