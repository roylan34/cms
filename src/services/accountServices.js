import $ from 'jquery';
import { API_URL } from '../helpers/constant';

class AccountServices {


    static getAccountById(stateForm) {
        const { id, actionForm } = stateForm;
        let data = null;
        $.ajax({
            type: 'POST',
            url: API_URL + '/get_accounts.php',
            data: { action: actionForm, id: id },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }


}

export default AccountServices;