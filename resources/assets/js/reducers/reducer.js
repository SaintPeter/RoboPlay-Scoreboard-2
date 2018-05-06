import { combineReducers } from 'redux';
import update from 'immutability-helper';

import {
    UPDATE_PAGE_TITLE,
    UPDATE_BACK_BUTTON,
} from '../actions/Generic';

import {
    SAVE_FAVORITE,
    DELETE_FAVORITE,
} from '../actions/TeamList';

import {
    SAVE_CHALLENGE_DATA,
    CLEAR_CHALLENGE_DATA,
} from '../actions/ChallengeData';

import {
    SCORE_CHALLENGE,
    ABORT_CHALLENGE, UPDATE_SCORE_SUMMARY, SUBMIT_SCORES,
    UPDATE_SCORES_SAVED_STATUS
} from '../actions/ScoreChallenge';

import {
    ADD_RUN,
    ADD_ABORT,
    CLEAR_RUNS,
    SAVE_LOADED_RUNS
} from '../actions/Runs';

function generic(state = { backURL: '/', title: 'Choose Competition'}, action) {
    switch (action.type) {
        case UPDATE_BACK_BUTTON:
            return Object.assign({}, state, { backURL: action.newURL, backShow: action.show });
        case UPDATE_PAGE_TITLE:
            return Object.assign({}, state, {title: action.newTitle });
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
        case SAVE_CHALLENGE_DATA:
            console.log("Challenge Data Saved");
            let temp = {};
            temp[action.year] = { $set: {[action.level]: action.data }};
            return update(state, temp);
        case CLEAR_CHALLENGE_DATA:
            console.log("Challenge Data Cleared");
            return {};
        default:
            return state;
    }
}

function teamScores(state = {}, action) {
    let currentScores;
    switch (action.type) {
        case SCORE_CHALLENGE:
            currentScores = state[action.teamId] ? state[action.teamId] : [];
            return Object.assign({}, state, {
                [action.teamId]: [
                    ...currentScores,
                    {
                        teamId: action.teamId,
                        chalId: action.chalId,
                        divId: action.divId,
                        timestamp: Math.floor(Date.now() / 1000),
                        abort: false,
                        saved: false,
                        scores: action.scores
                    }
                ]
            });
        case ABORT_CHALLENGE:
            currentScores = state[action.teamId] ? state[action.teamId] : [];
            return Object.assign({}, state, {
                [action.teamId]: [
                    ...currentScores,
                    {
                        teamId: action.teamId,
                        chalId: action.chalId,
                        divId: action.divId,
                        timestamp: Math.floor(Date.now() / 1000),
                        abort: true,
                        saved: false,
                        elementCount: action.elementCount
                    }
                ]
            });
        case UPDATE_SCORES_SAVED_STATUS:
            let scoreUpdates = Object.keys(state).reduce((teamScores, teamId) => {
                let updates = state[teamId].reduce((acc, score, index) => {
                    if(action.updates.indexOf(score.timestamp) > -1) {
                        acc[index] = { saved: { $set: true } };
                    }
                    return acc;
                }, {});
                if(Object.keys(updates).length > 0) {
                    teamScores[teamId] = updates;
                }
                return teamScores;
            }, {} );
            if(Object.keys(scoreUpdates).length > 0) {
                return update(state,scoreUpdates);
            }
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

function runs(state = {}, action) {
    switch(action.type) {
        case SAVE_LOADED_RUNS:
            return Object.assign({}, state, action.data);
        case ADD_RUN:
            const rkey = `${action.teamId}_${action.chalId}_runs`;
            return Object.assign({}, state, {
                [rkey]: (state[rkey]) ? state[rkey] + 1 : 1                
            });
        case ADD_ABORT:
            const key = `${action.teamId}_${action.chalId}_`;
            return Object.assign({}, state, {
                [key + 'runs']: (state[key + 'runs']) ? state[key + 'runs'] + 1 : 1,
                [key + 'aborts']: (state[key + 'aborts']) ? state[key + 'aborts'] + 1 : 1
            });
        case CLEAR_RUNS:
            console.log("Runs Data Cleared");
            return {};
        default:
            return state;
    }
}



const reducer = combineReducers({
    generic,
    teamList,
    challengeData,
    teamScores,
    scoreSummary,
    runs,
});

export default reducer;