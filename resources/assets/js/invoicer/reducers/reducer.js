import { combineReducers } from 'redux';

import activeYear from './activeYear'
import invoiceData from './invoiceData'
import showVideosList from "./showVideosList";

const reducer = combineReducers({
  activeYear,
  invoiceData,
  showVideosList
});

export default reducer;