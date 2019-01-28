export function toggleShowTeam(id) {
  return {
    type: 'TOGGLE_SHOW_TEAM',
    id
  }
}

export const clearShowTeams = ()  => {
  return {
    type: 'CLEAR_SHOW_TEAMS'
  }
};

const showTeamsList = (state = {}, action) => {
  switch(action.type) {
    case 'TOGGLE_SHOW_TEAM':
      if(state.hasOwnProperty(action.id)) {
        let temp = Object.assign({},state);
        delete temp[action.id];
        return temp;
      } else {
        return Object.assign({}, state, {[action.id]: 1});
      }
    case 'CLEAR_SHOW_TEAMS':
      return {};
    default:
      return state;
  }
};

export default showTeamsList;