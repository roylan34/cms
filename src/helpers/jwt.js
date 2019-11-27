import Cookies from './cookies';
import { API_URL } from './constant';
import $ from 'jquery';
import { Base64 } from 'js-base64';

class Jwt {

    constructor() {
        //Preserve state of cookies token.
        this.c_token = null;
        this.parse_token = null;
    }

    static get(name) {
        //Get cookies.
        if (!this.c_token) {
            this.c_token = Cookies.get('token');
            //Check if token stored in cookies.
            if (!this.c_token) {
                return null;
            }
            const data = this.c_token.split('.');
            const payload = Base64.decode(data[1]);
            this.parse_token = JSON.parse(payload);
        }
        //Check token name if exist.
        if (!this.parse_token.hasOwnProperty(name)) {
            throw new Error('Invalid token name');
        }
        return this.parse_token[name];
    }

    static serverVerify() {
        const xhr = $.ajax({
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
        this.parse_token = null;
    }

}

export default Jwt;