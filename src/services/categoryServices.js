import $ from 'jquery';
import { API_URL } from '../helpers/constant';

class CategoryServices {


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

    static getCategoryById(stateForm) {
        const { id, actionForm } = stateForm;
        let data = null;
        $.ajax({
            type: 'POST',
            url: API_URL + '/get_category.php',
            data: { action: actionForm, id: id },
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }
}

export default CategoryServices;