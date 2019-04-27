export const ERROR_AUTH_SET   = "ERROR_AUTH_SET";
export const ERROR_AUTH_CLEAR = "ERROR_AUTH_CLEAR";

export const ERROR_COMM_INC   = "ERROR_COMM_INC";
export const ERROR_COMM_CLEAR = "ERROR_COMM_CLEAR";

export const errorAuthSet = () => {
  return {
    type: ERROR_AUTH_SET
  }
};

export const errorAuthClear = () => {
  return {
    type: ERROR_AUTH_CLEAR
  }
};

export const errorCommInc = () => {
  return {
    type: ERROR_COMM_INC
  }
};

export const errorCommClear = () => {
  return {
    type: ERROR_COMM_CLEAR
  }
};

export const errorsClear = () =>
  (dispatch) => {
    console.log("Errors Cleared");
    dispatch(errorCommClear());
    dispatch(errorAuthClear());
  };