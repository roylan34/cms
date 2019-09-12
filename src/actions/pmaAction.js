//Action type
export const ADD_TONER_DESCRIPTION = 'ADD_TONER_DESCRIPTION'
export const ADD_TONER_PRICE = 'ADD_TONER_PRICE'
export const REMOVE_TONER_DESCRIPTION = 'REMOVE_TONER_DESCRIPTION'
export const REMOVE_TONER_PRICE = 'REMOVE_TONER_PRICE'

export const INPUT_TONER_INITIAL = 'INPUT_TONER_INITIAL'
export const INPUT_TONER_PRICE = 'INPUT_TONER_PRICE'

//Action creator
export function addToner() {
    return {
        type: ADD_TONER_DESCRIPTION
    }
}
export function removeToner(idx) {
    return {
        type: REMOVE_TONER_DESCRIPTION,
        idx
    }
}
export function addTonerPrice(idx) {
    return {
        type: ADD_TONER_PRICE,
        idx
    }
}
export function removeTonerPrice(dscrpIdx, priceIdx) {
    return {
        type: REMOVE_TONER_PRICE,
        dscrpIdx,
        priceIdx

    }
}
export function inputTonerInitial(idx, name, value) {
    return {
        type: INPUT_TONER_INITIAL,
        payload: {
            idx,
            name,
            value
        }
    }
}
export function inputTonerProce(idx, name, value) {
    return {
        type: INPUT_TONER_PRICE,
        payload: {
            idx,
            name,
            value
        }
    }
}

