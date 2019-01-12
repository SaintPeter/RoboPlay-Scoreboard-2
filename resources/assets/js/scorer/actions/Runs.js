export const ADD_RUN = 'ADD_RUN';
export const ADD_ABORT = 'ADD_ABORT';
export const SAVE_LOADED_RUNS = 'SAVE_LOADED_RUNS';
export const CLEAR_RUNS = 'CLEAR_RUNS';

export function addRun(teamId, chalId) {
    return {
        type: ADD_RUN,
        teamId, chalId
    }
}

export function addAbort(teamId, chalId) {
    return {
        type: ADD_ABORT,
        teamId, chalId
    }
}

export function clearRuns() {
    return {
        type: CLEAR_RUNS
    }
}

export function loadRuns(teamId) {
    return (dispatch, getState) => {
        let state = getState();
        if(!state.runs.hasOwnProperty(teamId)) {
            console.log("Loading Runs for " + teamId);
            return axios.get(
                '/api/scorer/runs/' + teamId,
                {headers: {'Content-Type': 'application/json'}})
                .then(updates => {
                    dispatch(saveLoadedRuns(updates.data.reduce((acc, run) => {
                        acc[run.rkey + 'runs'] = run.runs;
                        acc[run.rkey + 'aborts'] = parseInt(run.aborts);
                        return acc;
                    }, {
                        [teamId]: Date.now()
                    })));
                    console.log("Runs Loaded for " + teamId);
                })
                .catch((error) => {
                    console.error("Load Runs Error: " + error);
                });
        } else {
            console.log("No Need to Load Runs " + teamId);
        }
    }
}

export const saveLoadedRuns = (data) =>
    (dispatch) => {
        dispatch( {
            type: SAVE_LOADED_RUNS,
            data
        });
    };
