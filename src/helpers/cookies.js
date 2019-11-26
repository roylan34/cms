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

export default Cookies;