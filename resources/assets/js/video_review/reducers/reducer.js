import { combineReducers } from 'redux';

import activeYear from './activeYear'
import reviewStatus from './reviewStatus'

const reducer = combineReducers({
  activeYear,
  reviewStatus
});

export default reducer;