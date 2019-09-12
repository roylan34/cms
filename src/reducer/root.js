import { combineReducers } from 'redux';
import pmaReducer from './pmaReducer';


const rootReducer = combineReducers({
    pmaReducer: pmaReducer
});

export default rootReducer;