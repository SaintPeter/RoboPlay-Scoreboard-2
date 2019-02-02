export function toggleShowAllTeams() {
  return {
    type: 'TOGGLE_SHOW_ALL_TEAMS'
  }
}

const showAllTeams = (state = false, action) => {
  switch(action.type) {
    case 'TOGGLE_SHOW_ALL_TEAMS':
      return !state;
    default:
      return state;
  }
};

export default showAllTeams;