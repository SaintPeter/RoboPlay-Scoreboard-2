export function setYearLoading(year, value) {
  return {
    type: "SET_YEAR_LOADING",
    year,
    value
  }
}

const yearLoading = (state=false, action) => {
  switch(action.type) {
    case 'SET_YEAR_LOADING':
      return action.value;
    default:
      return state;
  }
};

export default yearLoading;