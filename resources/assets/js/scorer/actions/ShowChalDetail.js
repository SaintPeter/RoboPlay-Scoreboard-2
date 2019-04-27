export const SHOW_CHAL_SET   = "SHOW_CHAL_SET";
export const SHOW_CHAL_CLEAR = "SHOW_CHAL_CLEAR";

export const showChalSet = () => {
  return {
    type: SHOW_CHAL_SET
  }
};

export const showChalClear = () => {
  return {
    type: SHOW_CHAL_CLEAR
  }
};