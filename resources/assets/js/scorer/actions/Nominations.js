import errorHandler from "./errorHandler";
import {errorsClear} from "./Errors";

export const UPDATE_NOMINATION = 'UPDATE_NOMINATION';

export function updateNomination(teamId, values) {
  return {
    type: UPDATE_NOMINATION,
    teamId,
    values
  }
}

export function saveNominations(teamId, values) {
  return (dispatch) => {
    console.log('Saving Noms for Team: ', teamId, 'Values:', values);
    return window.axios.post(`/api/scorer/save_noms/${teamId}`, values)
      .then((response) => {
        console.log('Noms Saved, Updating');
        dispatch(updateNomination(teamId, values));
        dispatch(errorsClear());
      })
      .catch((err) => {
        errorHandler('Nomination Save', err, dispatch);
      })
  }
}

export function loadNominations(teamId) {
  return (dispatch) => {
    console.log(`Loading team`);
    return window.axios.get(`/api/scorer/noms/${teamId}`)
      .then((response) => {
        if(Object.keys(response.data).length > 2) {
          console.log(`Updating Team ${teamId}, Values: `, response.data);
          dispatch(updateNomination(teamId,response.data));
        } else {
          dispatch(updateNomination(teamId,{
            spirit: 0,
            teamwork: 0,
            persevere: 0,
          }));
        }
        dispatch(errorsClear());
      }).catch((err) => {
        errorHandler('Nomination Load', err, dispatch);
      })
  }
}