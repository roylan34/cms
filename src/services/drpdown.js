import $ from 'jquery';
import { API_URL } from './../helpers/constant';

class DrpDown {

    static getActiveCategories() {
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/get_category.php',
            data: { action: 'all-active' },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }

    static getListComp(comp_name) {
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/get_list_comp.php',
            data: { comp_name: comp_name },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }
    static getCompnameById(id) {
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/get_comp_id.php',
            data: { id: id },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }

}

export default DrpDown;