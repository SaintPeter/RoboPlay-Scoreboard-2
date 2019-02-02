import update from 'immutability-helper';

export function setInvoiceData(year, data) {
  return {
    type: 'SET_YEAR_DATA',
    year,
    data
  }
}

export function setTeamDivision(year,invoiceId,teamId,newDivision) {
  return {
    type: 'SET_TEAM_DIVISION',
    year,
    invoiceId,
    teamId,
    newDivision
  }
}

export function toggleTeamChecked(year,invoiceId,teamId) {
  return {
    type: 'TOGGLE_TEAM_CHECKED',
    year,
    invoiceId,
    teamId
  }
}

export function updateTeamCheckedCounts(year, invoiceId,teamId) {
  return {
    type: 'UPDATE_TEAM_CHECKED_COUNTS',
    year,
    invoiceId,
    teamId
  }
}

export function toggleTeamCheckedAndRecalc(year,invoiceId,teamId) {
  return (dispatch) => {
    dispatch(toggleTeamChecked(year,invoiceId,teamId));
    dispatch(updateTeamCheckedCounts(year,invoiceId,teamId));
  }
}

export function setVideoDivision(year,invoiceId,videoId,newDivision) {
  return {
    type: 'SET_VIDEO_DIVISION',
    year,
    invoiceId,
    videoId,
    newDivision
  }
}

export function setPaidNotes(year, invoiceId,paid,notes) {
  return {
    type: 'UPDATE_PAID_NOTES',
    year,
    invoiceId,
    paid,
    notes
  }
}

export const updatePaidNotes = (invoiceId, paid, notes) => (dispatch, getState) => {
  const year = getState().activeYear;

  return window.axios.post(`/api/invoicer/update_paid_notes/${invoiceId}/${paid}`, {notes})
    .then((response) => {
      console.log(`Update Paid Notes - Invoice ${invoiceId}, Paid: ${paid}, Notes: ${notes}`);
      dispatch(setPaidNotes(year, invoiceId, paid, notes));
    })
    .catch((err) => {
      console.log(`Error Updating Paid Notes - Invoice ${invoiceId}, Paid: ${paid}, Notes: ${notes}\n${err}`)
    })
};

export const updateTeamDivision = (invoiceId,teamId,newDivision) => (dispatch, getState) => {
  const year = getState().activeYear;

  console.log(`Updating Year: ${year}, Team: ${teamId}, Division: ${newDivision}`);
  return window.axios.get('/api/invoicer/save_team_div/' + teamId + "/" + newDivision)
    .then((response) => {
      dispatch(setTeamDivision(year,invoiceId,teamId,newDivision));
    })
    // .catch((err) => {
    //   console.log(`Error updating team ${teamId} to division ${newDivision}: ${err}`)
    // })
};

export const updateTeamChecked = (invoiceId,teamId) => (dispatch, getState) => {
  const year = getState().activeYear;
  console.log(`Toggle Checked Year: ${year}, Team: ${teamId}`);

  return window.axios.get('/api/invoicer/toggle_team/' + teamId)
    .then((response) => {
      dispatch(toggleTeamCheckedAndRecalc(year,invoiceId,teamId));
    })
  // .catch((err) => {
  //   console.log(`Error updating team ${teamId} to division ${newDivision}: ${err}`)
  // })
};

export const updateVideoDivision = (invoiceId,videoId,newDivision) => (dispatch, getState) => {
  const year = getState().activeYear;

  console.log(`Updating Year: ${year}, Video: ${videoId}, Division: ${newDivision}`);
  return window.axios.get('/api/invoicer/save_video_div/' + videoId + "/" + newDivision)
    .then((response) => {
      dispatch(setVideoDivision(year,invoiceId,videoId,newDivision));
    })
  // .catch((err) => {
  //   console.log(`Error updating video ${videoId} to division ${newDivision}: ${err}`)
  // })
};

export const fetchInvoiceData = (year) => (dispatch, getState) => {
  //const {InvoiceData} = getState();

  return window.axios.get('/api/invoicer/invoice_list/' + year)
    .then((response) => {
      console.log(`Invoice Data ${year} Fetched`);
      return response.data;
    })
    .then((data) => {
      console.log('Saving Invoice Data');
      dispatch(setInvoiceData(year, data));
    })
    .then(() => Promise.resolve('Data Saved'))
    // .catch( (error) => {
    //   console.log("Error loading Invoice data from remote: " + error);
    // });
};


const invoiceData = (state = {}, action) => {
  let teamIndex = -1;
  let stateUpdate = {};
  switch(action.type) {
    case 'SET_YEAR_DATA':
      return Object.assign({}, state, {[action.year]: action.data});
    case 'SET_TEAM_DIVISION':
      teamIndex = state[action.year].team_data[action.invoiceId].findIndex(team => team.id === action.teamId);

      if(teamIndex < 0) {
        console.log(`Error: Unable to find team ${action.teamId} in Invoice ${action.invoiceId}`);
        return state;
      }
      stateUpdate = { [action.year]: {
        team_data: {
          [action.invoiceId]:{
            [teamIndex]:{
              division_id: {
                $set: action.newDivision
              }
            }
          }
        }
      }};
      return update(state,stateUpdate);
    case 'TOGGLE_TEAM_CHECKED':
      teamIndex = state[action.year].team_data[action.invoiceId].findIndex(team => team.id === action.teamId);

      if(teamIndex < 0) {
        console.log(`Error: Unable to find team ${action.teamId} in Invoice ${action.invoiceId}`);
        return state;
      }
      stateUpdate = { [action.year]: {
          team_data: {
            [action.invoiceId]:{
              [teamIndex]:{
                $toggle: ['status']
              }
            }
          }
        }};
      return update(state,stateUpdate);
    case 'UPDATE_TEAM_CHECKED_COUNTS':
      const teamCounts = state[action.year].team_data[action.invoiceId].reduce((carry, team) => {
        carry[team.status ? 1 : 0]++;
        return carry;
      },[0,0]);
      stateUpdate = { [action.year]: {
         invoices: {
           [action.invoiceId]: {
             teams_checked: { $set: teamCounts[1] },
             teams_unchecked: { $set: teamCounts[0] }
           }
         }
      }};
      return update(state, stateUpdate);
    case 'SET_VIDEO_DIVISION':
      const videoIndex = state[action.year].video_data[action.invoiceId].findIndex(video => video.id === action.videoId);

      if(videoIndex < 0) {
        console.log(`Error: Unable to find video ${action.videoId} in Invoice ${action.invoiceId}`);
        return state;
      }
      stateUpdate = { [action.year]: {
          video_data: {
            [action.invoiceId]:{
              [videoIndex]:{
                vid_division_id: {
                  $set: action.newDivision
                }
              }
            }
          }
        }};
      return update(state,stateUpdate);
    case 'UPDATE_PAID_NOTES':
      stateUpdate = { [action.year]: {
          invoices: {
            [action.invoiceId]:{
              paid: { $set: action.paid },
              notes: { $set: action.notes }
            }
          }
        }};
      return update(state, stateUpdate);
    default:
      return state;
  }
};

export default invoiceData;