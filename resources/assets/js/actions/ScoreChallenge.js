export const LOAD_CHALLENGE_DATA =        'LOAD_CHALLENGE_DATA';
export const SCORE_CHALLENGE =            'SCORE_CHALLENGE';
export const ABORT_CHALLENGE =            'ABORT_CHALLENGE';
export const UPDATE_SCORE_SUMMARY =       'UPDATE_SCORE_SUMMARY';
export const SUBMIT_SCORES =              'SUBMIT_SCORES';
export const UPDATE_SCORES_SAVED_STATUS = 'UPDATE_SCORES_SAVED_STATUS';

export function loadChallengeData(year, level, data) {
    return {
        type: LOAD_CHALLENGE_DATA,
        year, level, data
    }
}

export function scoreChallenge(teamId, chalId, scores) {
    return {
        type: SCORE_CHALLENGE,
        teamId, chalId, scores
    }
}

export function abortChallenge(teamId, chalId) {
    return {
        type: ABORT_CHALLENGE,
        teamId, chalId
    }
}

export function updateScoreSummary(teamScores) {
    return {
        type: UPDATE_SCORE_SUMMARY,
        teamScores
    }
}

export function updateScoresSavedStatus(updates) {
    return {
        type: UPDATE_SCORES_SAVED_STATUS,
        updates
    }
}

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
            axios.post(
                '/api/scorer/save_scores',
                { scores: submitData },
                {headers: {'Content-Type': 'application/json'}})
            .then(updates => {
                dispatch(updateScoresSavedStatus(updates.data));
            })
            .catch((error) => {
                console.error("Submit Scores Error: " + error);
            });
        }
    }
}

