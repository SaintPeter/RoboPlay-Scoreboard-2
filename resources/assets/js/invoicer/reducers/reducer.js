import { combineReducers } from 'redux';

import activeYear from './activeYear'
import invoiceData from './invoiceData'
import showVideosList from "./showVideosList";
import showTeamsList from "./showTeamsList";

const reducer = combineReducers({
  activeYear,
  invoiceData,
  showVideosList,
  showTeamsList
});

export default reducer;