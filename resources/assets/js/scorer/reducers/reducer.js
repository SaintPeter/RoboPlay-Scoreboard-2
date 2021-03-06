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
    ABORT_CHALLENGE, UPDATE_SCORE_SUMMARY,
    UPDATE_SCORES_SAVED_STATUS, CLEAR_SCORES, CLEAR_SCORE_SUMMARY, SAVE_LOADED_SCORES
} from '../actions/ScoreChallenge';

import {
    ADD_RUN,
    ADD_ABORT,
    CLEAR_RUNS,
    SAVE_LOADED_RUNS
} from '../actions/Runs';

import {
  UPDATE_NOMINATION
} from "../actions/Nominations";

import {ERROR_AUTH_CLEAR, ERROR_AUTH_SET, ERROR_COMM_CLEAR, ERROR_COMM_INC} from "../actions/Errors";
import {SAVING_SCORES_CLEAR, SAVING_SCORES_SET} from "../actions/SavingScores";
import {SHOW_CHAL_CLEAR, SHOW_CHAL_SET} from "../actions/ShowChalDetail";

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
        case SAVE_LOADED_SCORES:
            const unsaved = (state[action.teamId]) ? state[action.teamId].filter((score) => score.saved === false) : [];
            const newScores = { [action.teamId]: action.scores[action.teamId].concat(unsaved) };
            return Object.assign({}, state, newScores);
        case CLEAR_SCORES:
            return {};
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
        case CLEAR_SCORE_SUMMARY:
            return { s: 0, u: 0 };
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

function nominations(state = {}, action) {
  switch(action.type) {
    case UPDATE_NOMINATION:
      return Object.assign({}, state, {[action.teamId]: action.values});
    default:
      return state;
  }
}

function errorAuth(state = false, action) {
  switch(action.type) {
    case ERROR_AUTH_SET:
      return true;
    case ERROR_AUTH_CLEAR:
      return false;
    default:
      return state;
  }
}

function showChalDetail(state = true, action) {
  switch(action.type) {
    case SHOW_CHAL_SET:
      return true;
    case SHOW_CHAL_CLEAR:
      return false;
    default:
      return state;
  }
}

function savingScores(state = false, action) {
  switch(action.type) {
    case SAVING_SCORES_SET:
      return true;
    case SAVING_SCORES_CLEAR:
      return false;
    default:
      return state;
  }
}

function errorComm(state = 0, action) {
  switch(action.type) {
    case ERROR_COMM_INC:
      return state + 1;
    case ERROR_COMM_CLEAR:
      return 0;
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
  nominations,
  errorAuth,
  errorComm,
  savingScores,
  showChalDetail,
});

export default reducer;