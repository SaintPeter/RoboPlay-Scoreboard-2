export const SAVING_SCORES_SET = "SAVING_SCORES_SET";
export const SAVING_SCORES_CLEAR = "SAVING_SCORES_CLEAR";

export const savingScoresSet = () => {
  return {
    type: SAVING_SCORES_SET
  }
};

export const savingScoresClear = () => {
  return {
    type: SAVING_SCORES_CLEAR
  }
};