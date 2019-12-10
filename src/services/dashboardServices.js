import $ from 'jquery';
import { API_URL } from '../helpers/constant';

class Dashboard {

    static getCalendarForecast(user_id, valid_to) {
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/dashboard.php',
            data: { user_id, valid_to, action: 'calendar' },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }
}

export default Dashboard;