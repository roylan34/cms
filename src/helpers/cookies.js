class Cookies {

    /**
     * 
     * @param {string} name - Set cookie name.
     * @param {*} value - Set a value.
     * @param {number} days - Number of days to expire.
     */
    static set(name, value, days) {
        let expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    }
    /**
     * 
     * @param {string} name - Cookie name use by retrieving the value.
     * @return {any} Cookie value.
     */
    static get(name) {
        let cs = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            c = c.trim();
            // while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(cs) == 0) return c.substring(cs.length, c.length);
        }
        return null;
    }
    /**
     * 
     * @param {string} name  - Remove cookie.
     */
    static clear(name) {
        this.set(name, "", -1);
    }
}

// class jwt {

//     constructor() {
//         //Preserve state of cookies token.
//         this.c_token = null;
//     }

//     static get(input) {
//         //Get cookies.
//         if (!this.c_token) {
//             this.c_token = Cookies.get('token');
//             //Check if token stored in cookies.
//             if (!this.c_token) {
//                 return null;
//             }
//         }

//         var token = this.c_token.split('.');
//         var payload = Base64.decode(token[1]);
//         var parsePayload = JSON.parse(payload);

//         //Check token name if exist.
//         if (!parsePayload.aaData.hasOwnProperty(input)) {
//             throw new Error('Invalid token name');
//         }
//         return parsePayload.aaData[input];
//     }

//     static serverVerify() {
//         var xhr = $.ajax({
//             async: false,
//             url: assets + 'php/jwtInvokeVerify.php',
//         });

//         if (xhr.status == "401") {
//             alert('Access not allowed. \n Please contact the developer to assist you. Thanks!');
//             return false;
//         }
//         return true;
//     }

//     static clear() {
//         this.c_token = null;
//         return this.c_token;
//     }

// }

// export default jwt;
export default Cookies;