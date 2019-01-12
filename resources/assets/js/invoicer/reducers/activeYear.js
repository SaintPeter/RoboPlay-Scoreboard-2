export function setActiveYear(newYear) {
  return {
    type: 'SET_ACTIVE_YEAR',
    newYear: newYear
  }
}


const activeYear = (state = 0, action) => {
  switch(action.type) {
    case 'SET_ACTIVE_YEAR':
      return action.newYear;
    default:
      return state;
  }
};

export default activeYear;