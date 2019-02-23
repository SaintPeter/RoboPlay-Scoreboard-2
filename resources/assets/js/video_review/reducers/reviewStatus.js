
export function setReviewStatus(year, data) {
  return {
    type: 'SET_REVIEW_STATUS',
    year,
    data
  }
}

export const loadReviewStatus = () => (dispatch, getState) => {
  const year = getState().activeYear;

  console.log(`Load Review Status - Year: ${year}`);
  return window.axios.get(`/api/video_review/${year}/review_status`)
    .then((response) => {
      console.log(`Review Status for ${year} Fetched`);
      return response.data;
    })
    .then((data) => {
      console.log('Saving Invoice Data');
      dispatch(setReviewStatus(year, data));
    })
    .then(() => Promise.resolve('Data Saved'))
    // .catch( (error) => {
    //   console.log("Error loading Invoice data from remote: " + error);
    // });
};


export default function reviewStatus(state = {}, action) {
  switch(action.type) {
    case 'SET_REVIEW_STATUS':
      return Object.assign({}, state, action.data);
    default:
      return state;
  }
}