import $ from 'jquery';
import { API_URL } from '../helpers/constant';

class CurrentServices {


    static getCurrentById(stateForm) {
        const { id, actionForm } = stateForm;
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/get_current.php',
            data: { action: actionForm, id: id },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }

    static updateStatus(id, status) {
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/action_current.php',
            data: { id, status, action: 'update_status' },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }


}

export default CurrentServices;