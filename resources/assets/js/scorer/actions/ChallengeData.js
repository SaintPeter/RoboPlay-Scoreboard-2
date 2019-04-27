import {errorsClear} from "./Errors";
import errorHandler from "./errorHandler";

export const SAVE_CHALLENGE_DATA =        'SAVE_CHALLENGE_DATA';
export const CLEAR_CHALLENGE_DATA =       'CLEAR_CHALLENGE_DATA';

export function saveChallengeData(year, level, data) {
    return {
        type: SAVE_CHALLENGE_DATA,
        year, level, data
    }
}

export function clearChallengeData() {
    return {
        type: CLEAR_CHALLENGE_DATA
    }
}

export const loadChallengeData = (year, level) => (dispatch, getState) => {
            const {challengeData} = getState();
            if (!(challengeData[year] && challengeData[year][level])) {
                console.log(`Loading Challenge Data ${year}, Level ${level} . . .`);
                return window.axios.get('/api/challenges/' + year + "/" + level)
                    .then((response) => {
                        console.log(`Challenge Data ${year}, Level ${level} Fetched`);
                        return response.data;
                    })
                    .then((data) => {
                        console.log('Saving Data');
                        dispatch(saveChallengeData(year, level,data));
                        dispatch(errorsClear());
                    })
                    .then(() => Promise.resolve('Data Saved'))
                    .catch( (error) => {
                        errorHandler('Load Challenge Data', error, dispatch);
                    });
            } else {
                console.log(`No need to load Challenge Data ${year}, Level ${level}`);
                return Promise.resolve('No Load Needed');
            }
    };