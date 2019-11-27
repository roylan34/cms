import { combineReducers } from 'redux';
import currentReducer from './currentReducer';
import categoryReducer from './categoryReducer';
import drpReducer from './drpReducer';
import activityLogsReducer from './activityLogsReducer';
import searchReducer from './searchReducer';
import accountReducer from './accountReducer';
import userDetailsReducer from './userDetailsReducer';


const rootReducer = combineReducers({
    currentReducer,
    categoryReducer,
    drpReducer,
    activityLogsReducer,
    searchReducer,
    accountReducer,
    userDetailsReducer
});

export default rootReducer;