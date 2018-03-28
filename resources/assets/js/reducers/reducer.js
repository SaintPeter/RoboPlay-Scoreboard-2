import { combineReducers } from 'redux';

import {
    UPDATE_PAGE_TITLE,
    UPDATE_BACK_BUTTON,
} from '../actions/Generic';

import {
    SAVE_FAVORITE,
    DELETE_FAVORITE,
} from '../actions/TeamList';

import {
    LOAD_CHALLENGE_DATA,
    SCORE_CHALLENGE,
    ABORT_CHALLENGE, UPDATE_SCORE_SUMMARY, SUBMIT_SCORES,
    UPDATE_SCORES_SAVED_STATUS
} from '../actions/ScoreChallenge';

function generic(state = { backURL: '/', title: 'Choose Competition'}, action) {
    switch (action.type) {
        case UPDATE_BACK_BUTTON:
            return Object.assign({}, state, { backURL: action.newURL });
        case UPDATE_PAGE_TITLE:
            return Object.assign({}, state, {title: action.title });
        default:
            return state;
    }
}

function teamList(state = { teamFavorites: {} }, action) {
    switch (action.type) {
        case SAVE_FAVORITE:
            return Object.assign({}, state, {
                teamFavorites: Object.assign({}, state.teamFavorites, { [action.id]: 1 })
            });
        case DELETE_FAVORITE:
            let newTeamFavorites = Object.assign({}, state.teamFavorites);
            delete newTeamFavorites[action.id];
            return Object.assign({}, state, {
                teamFavorites: newTeamFavorites
            });
        default:
            return state;
    }
}

function challengeData(state = {} , action) {
    switch (action.type) {
        case LOAD_CHALLENGE_DATA:
            let temp = {};
            temp[action.year] = {};
            temp[action.year][action.level] = action.data;
            return Object.assign({}, state, temp);
        default:
            return state;
    }
}

function teamScores(state = {}, action) {
    switch (action.type) {
        case SCORE_CHALLENGE:
            const currentScores = state[action.teamId] ? state[action.teamId] : [];
            return Object.assign({}, state, {
                [action.teamId]: [
                    ...currentScores,
                    {
                        teamId: action.teamId,
                        chalId: action.chalId,
                        timestamp: Math.floor(Date.now() / 1000),
                        abort: false,
                        saved: false,
                        scores: action.scores
                    }
                ]
            });
        case ABORT_CHALLENGE:

        case UPDATE_SCORES_SAVED_STATUS:
            return state;
        default:
            return state;
    }
}

function scoreSummary(state = { s: 0, u: 0 }, action) {
    switch (action.type) {
        case UPDATE_SCORE_SUMMARY:
            let scoreSummary = { s: 0, u: 0 };
            if(action.teamScores) {
                scoreSummary = Object.keys(action.teamScores).reduce( (summary, teamId)=> {
                    let scores = action.teamScores[teamId].reduce((saved, scoreRecord) => {
                        saved[ scoreRecord.saved === true ? 1 : 0 ]++;
                        return saved;
                    },[0,0]);
                    summary.u += scores[0];
                    summary.s += scores[1];
                    return summary;
                }, scoreSummary );
            }
            return scoreSummary;
        default:
            return state;
    }
}

const reducer = combineReducers({
    generic,
    teamList,
    challengeData,
    teamScores,
    scoreSummary
});

export default reducer;