import {errorAuthSet, errorCommInc} from "./Errors";


export default function errorHandler(type, error, dispatch) {
  if(error.response) {
    if(error.response.status == 401) {
      // Authentication error
      console.error(`${type} Auth`, error);
      dispatch(errorAuthSet());
      return;
    }
  }
  // All other errors
  console.error(`${type} Other`, error);
  dispatch(errorCommInc());
}