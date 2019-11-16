import { combineReducers } from 'redux';
import currentReducer from './currentReducer';
import categoriesReducer from './categoriesReducer';
import drpReducer from './drpReducer';


const rootReducer = combineReducers({
    currentReducer: currentReducer,
    categoriesReducer: categoriesReducer,
    drpReducer: drpReducer
});

export default rootReducer;