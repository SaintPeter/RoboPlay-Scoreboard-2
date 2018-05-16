export const SCORE_CHALLENGE =            'SCORE_CHALLENGE';
export const ABORT_CHALLENGE =            'ABORT_CHALLENGE';
export const CLEAR_SCORES =               'CLEAR_SCORES';
export const CLEAR_SCORE_SUMMARY =        'CLEAR_SCORE_SUMMARY';
export const UPDATE_SCORE_SUMMARY =       'UPDATE_SCORE_SUMMARY';
export const UPDATE_SCORES_SAVED_STATUS = 'UPDATE_SCORES_SAVED_STATUS';

export const scoreChallenge = (teamId, chalId, divId, scores) =>
    (dispatch, getState) => {
        dispatch({
            type: SCORE_CHALLENGE,
            teamId, chalId, divId, scores
        });
        const { teamScores } = getState();
        dispatch(updateScoreSummary(teamScores));
    };

export const abortChallenge = (teamId, chalId, divId, elementCount) =>
    (dispatch, getState) => {
        dispatch( {
            type: ABORT_CHALLENGE,
            teamId, chalId, divId, elementCount
        });
        const { teamScores } = getState();
        dispatch(updateScoreSummary(teamScores));
    };

export function updateScoreSummary(teamScores) {
    return {
        type: UPDATE_SCORE_SUMMARY,
        teamScores
    }
}

export function clearScores() {
    return {
        type: CLEAR_SCORES
    }
}

export function clearScoreSummary() {
    return {
        type: CLEAR_SCORE_SUMMARY
    }
}

export const  updateScoresSavedStatus = (updates) =>
    (dispatch, getState) => {
        dispatch( {
            type: UPDATE_SCORES_SAVED_STATUS,
            updates
        });
        const { teamScores } = getState();
        dispatch(updateScoreSummary(teamScores));
    };

export function submitScores(teamScores) {
    return (dispatch) => {
        let submitData = [];
        if (teamScores) {
            Object.keys(teamScores).forEach((teamId) => {
                Array.prototype.push.apply(submitData, teamScores[teamId].filter(score => {
                    return score.saved === false;
                }));
            });
        }
        if (submitData.length > 0) {
            console.log("Posting Scores");
            return axios.post(
                '/api/scorer/save_scores',
                { scores: submitData },
                {headers: {'Content-Type': 'application/json'}})
            .then(updates => {
                dispatch(updateScoresSavedStatus(updates.data));
            })
            .catch((error) => {
                console.error("Submit Scores Error: " + error);
            });
        } else {
            console.log("No Scores to Send");
            return Promise.resolve();
        }
    }
}

export function clearAllScores() {
    return (dispatch) => {
        dispatch(clearScores());
        dispatch(clearScoreSummary());
    }
}

