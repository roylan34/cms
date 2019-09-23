import Categories from './../services/categories.js';

//Action type
export const FETCH_ALL_CATEGORIES = 'FETCH_ALL_CATEGORIES'


//Action creator
export function fetchCategories() {
    const data = Categories.getAllCategories();
    return {
        type: FETCH_ALL_CATEGORIES,
        payload: data.aaData
    }
}

