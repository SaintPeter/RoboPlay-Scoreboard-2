
const CACHE_EXPIRE_NAME = 'compDataCacheTime';
const DATA_NAME = 'compData';
const CACHE_TIME = 1000 * 60 * 60 * 6; // 6 Hour Cache Time

export default class loadCompData {
    static load(stateVariable, stateVariableName) {
        let outputData = {};
        let hasLocalStorage = false;
        try {
            if(localStorage) {
                hasLocalStorage = true;
            }
        } catch (e) {
            console.log("No LocalStorage Support");
        }

        if(hasLocalStorage) {
            let cacheExpires = localStorage.getItem(CACHE_EXPIRE_NAME);
            // If we don't have a cache expires time
            // or we have not yet reached the expire time
            // Load data from localStorage and return it
            if(cacheExpires && cacheExpires > Date.now()) {
                // Load the data from localStorage
                let rawData = localStorage.getItem(DATA_NAME);
                if(rawData) {
                    try {
                        outputData[stateVariableName] = JSON.parse(rawData);
                        stateVariable.setState(outputData);
                        return;
                    } catch(e) {
                        console.log("Error parsing JSON data from localStorage: " + e.message);
                    }
                }
            }
        }

        // Remote Load
        window.axios.get('api/competition_list')
            .then(function(response) {
                let temp = {};
                temp[stateVariableName] = response.data;
                stateVariable.setState(temp);
                if(hasLocalStorage) {
                    localStorage.setItem(CACHE_EXPIRE_NAME, Date.now() + CACHE_TIME);
                    localStorage.setItem(DATA_NAME, JSON.stringify(response.data));
                }
            }).catch(function(error) {
                console.log("Error loading data from remote: " + error);
            });
    }

}