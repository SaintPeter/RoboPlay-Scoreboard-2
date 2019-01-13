import { combineReducers } from 'redux';

import activeYear from './activeYear'
import invoiceData from './invoiceData'

const reducer = combineReducers({
  activeYear,
  invoiceData,
});

export default reducer;