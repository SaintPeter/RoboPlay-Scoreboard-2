import { combineReducers } from 'redux';

import activeYear from './activeYear'
import invoiceData from './invoiceData'
import showVideosList from "./showVideosList";
import showTeamsList from "./showTeamsList";
import showAllVideos from "./showAllVideos";
import showAllTeams from "./showAllTeams";
import filterInvoiceBy from "./filterInvoiceBy";
import yearLoading from "./yearLoading";

const reducer = combineReducers({
  activeYear,
  invoiceData,
  showVideosList,
  showTeamsList,
  showAllVideos,
  showAllTeams,
  filterInvoiceBy,
  yearLoading
});

export default reducer;