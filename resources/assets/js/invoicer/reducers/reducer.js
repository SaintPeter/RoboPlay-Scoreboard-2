import { combineReducers } from 'redux';

function generic(state = {}, action) {
  return state;
}

const reducer = combineReducers({
  generic,
});

export default reducer;