export const LOAD_CHALLENGE_DATA = 'LOAD_CHALLENGE_DATA';
export const SCORE_CHALLENGE = 'SCORE_CHALLENGE';
export const ABORT_CHALLENGE = 'ABORT_CHALLENGE';


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

