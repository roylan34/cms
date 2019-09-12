import {
    ADD_TONER_DESCRIPTION,
    ADD_TONER_PRICE,
    REMOVE_TONER_DESCRIPTION,
    REMOVE_TONER_PRICE,
    INPUT_TONER_INITIAL,
    INPUT_TONER_PRICE
} from '../actions/pmaAction';


const initial_state = {
    addToner: [
        { initial: null, min: null, price: [{ model: null, price: null }] }
    ],
};

export default function pmaReducer(state = initial_state, action) {

    switch (action.type) {
        case ADD_TONER_DESCRIPTION:
            let newStateAddToner = [...state.addToner];
            newStateAddToner.push({ initial: null, min: null, price: [{ model: null, price: null }] })
            return {
                ...state,
                addToner: newStateAddToner
            }
            break;
        case REMOVE_TONER_DESCRIPTION:
            let newStateTonerDscrp = [...state.addToner];
            newStateTonerDscrp.splice(action.idx, 1);
            return {
                ...state,
                addToner: newStateTonerDscrp
            };
            break;
        case ADD_TONER_PRICE:
            let newStateTonerPrice = [...state.addToner];
            newStateTonerPrice[action.idx].price.push({ model: null, price: null })
            return {
                ...state,
                addToner: newStateTonerPrice
            };
            break;
        case REMOVE_TONER_PRICE:
            let newStateRemoveTonerPrice = [...state.addToner];
            newStateRemoveTonerPrice[action.dscrpIdx].price.splice(action.priceIdx, 1);
            return {
                ...state,
                addToner: newStateRemoveTonerPrice
            };
            break;
        case INPUT_TONER_INITIAL:
            let initial = { ...state };
            initial.addToner.filter((item, index) => {
                if (action.payload.idx === index) {
                    return item[action.payload.name] = action.payload.value
                }
                return item;
            });
            return {
                ...state,
                addToner: initial.addToner
            };
            break;
        case INPUT_TONER_PRICE:
            let price = { ...state };
            price.addToner[action.payload.idxInitial].price.filter((item, index) => {
                if (action.payload.idxPrice === index) {
                    return item[action.payload.name] = action.payload.value
                }
                return item;
            });
            return {
                ...state,
                addToner: price.addToner
            };
            break;
        default:
            return state;
            break;
    }
}