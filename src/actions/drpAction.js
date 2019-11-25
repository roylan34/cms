import Drpdown from './../services/drpdown.js';
import { isEmpty } from './../helpers/utils';

//Action type
export const FETCH_LIST_COMP = 'FETCH_LIST_COMP'
export const FETCH_COMP_NAME = 'FETCH_COMP_NAME'
export const HALT_FETCHING_COMP = 'HALT_FETCHING_COMP'


export function fetchComp(comp_name) {
    let data = null;
    let isFetching = null;
    if (!isEmpty(comp_name)) {
        data = Drpdown.getListComp(comp_name)['aaData'];
        isFetching = true;
    } else {
        data = [];
        isFetching = false;
    }

    return {
        type: FETCH_LIST_COMP,
        payload: data,
        isFetching: isFetching
    }
}
export function fetchCompById(id) {
    let data = null;
    if (!isEmpty(id)) {
        data = Drpdown.getCompnameById(id)['aaData'];
    } else {
        data = [];
    }

    return {
        type: FETCH_COMP_NAME,
        payload: data,
    }
}