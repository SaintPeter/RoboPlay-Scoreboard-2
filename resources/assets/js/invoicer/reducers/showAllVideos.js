export function toggleShowAllVideos() {
  return {
    type: 'TOGGLE_SHOW_ALL_VIDEOS'
  }
}

const showAllVideos = (state = false, action) => {
  switch(action.type) {
    case 'TOGGLE_SHOW_ALL_VIDEOS':
      return !state;
    default:
      return state;
  }
};

export default showAllVideos;