export function toggleShowVideo(id) {
  return {
    type: 'TOGGLE_SHOW_VIDEO',
    id
  }
}

export const clearShowVideos = ()  => {
  return {
    type: 'CLEAR_SHOW_VIDEOS'
  }
};

const showVideosList = (state = {}, action) => {
  switch(action.type) {
    case 'TOGGLE_SHOW_VIDEO':
      if(state.hasOwnProperty(action.id)) {
        let temp = Object.assign({},state);
        delete temp[action.id];
        return temp;
      } else {
        return Object.assign({}, state, {[action.id]: 1});
      }
    case 'CLEAR_SHOW_VIDEOS':
      return {};
    default:
      return state;
  }
};

export default showVideosList;