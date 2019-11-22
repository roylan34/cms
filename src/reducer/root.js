import { combineReducers } from 'redux';
import currentReducer from './currentReducer';
import categoriesReducer from './categoriesReducer';
import drpReducer from './drpReducer';
import activityLogsReducer from './activityLogsReducer';
import searchReducer from './searchReducer';
import accountReducer from './accountReducer';


const rootReducer = combineReducers({
    currentReducer,
    categoriesReducer,
    drpReducer,
    activityLogsReducer,
    searchReducer,
    accountReducer
});

export default rootReducer;