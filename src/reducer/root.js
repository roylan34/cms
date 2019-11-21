import { combineReducers } from 'redux';
import currentReducer from './currentReducer';
import categoriesReducer from './categoriesReducer';
import drpReducer from './drpReducer';
import activityLogsReducer from './activityLogsReducer';


const rootReducer = combineReducers({
    currentReducer,
    categoriesReducer,
    drpReducer,
    activityLogsReducer
});

export default rootReducer;