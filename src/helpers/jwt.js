import Cookies from './cookies';
import { Base64 } from 'js-base64';
import moment from 'moment';
class Jwt {

    constructor() {
        //Preserve state of cookies token.
        this.c_token = null;
        this.parse_token = null;
        this.expiration = null;
    }
    static getToken() {
        this.c_token = Cookies.get('token');
        //Check if token stored in cookies.
        if (!this.c_token) {
            Jwt.clear();
            return null;
        }
        const data = this.c_token.split('.');
        const payload = Base64.decode(data[1]);
        this.parse_token = JSON.parse(payload);
        this.expiration = this.parse_token.expr_timestamp;
    }

    static get(name) {
        let current_unix_date = moment().format('X');
        //Get cookies.
        //Set a new token if expired.
        if (!this.c_token) {
            Jwt.getToken();
        }

        if (current_unix_date > this.expiration) {
            Jwt.getToken();
        }

        //Check token name if exist.
        if (!this.parse_token.hasOwnProperty(name)) {
            throw new Error('Invalid token name');
        }
        return this.parse_token[name];
    }

    static clear() {
        this.c_token = null;
        this.parse_token = null;
        this.expiration = null;
    }

}

export default Jwt;