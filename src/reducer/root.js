import { combineReducers } from 'redux';
import currentReducer from './currentReducer';
import categoriesReducer from './categoriesReducer';


const rootReducer = combineReducers({
    currentReducer: currentReducer,
    categoriesReducer: categoriesReducer
});

export default rootReducer;