import $ from 'jquery';
import { API_URL } from './../helpers/constant';

class Categories {


    static getAllCategories() {
        let data = null;
        $.ajax({
            type: 'GET',
            url: API_URL + '/get_categories.php',
            cache: false,
            async: false,
        }).done(res => { data = res; });
        return data;
    }

}

export default Categories;