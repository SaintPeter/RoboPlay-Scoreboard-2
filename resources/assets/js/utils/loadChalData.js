
const CACHE_EXPIRE_PREFIX = 'chalDataCacheTime_';
const DATA_PREFIX = 'chalData_';
const CACHE_TIME = 1000 * 60 * 60 * 6; // 6 Hour Cache Time

export default class loadChalData {
    static load(year,level,callback) {
        let outputData = {};
        let hasLocalStorage = false;
        const chalId = year + '_' + level;

        try {
            if(localStorage) {
                hasLocalStorage = true;
            }
        } catch (e) {
            console.log("No LocalStorage Support");
        }

        if(hasLocalStorage) {
            let cacheExpires = localStorage.getItem(CACHE_EXPIRE_PREFIX + chalId);
            // If we don't have a cache expires time
            // or we have not yet reached the expire time
            // Load data from localStorage and return it
            if(cacheExpires && cacheExpires > Date.now()) {
                // Load the data from localStorage
                let rawData = localStorage.getItem(DATA_PREFIX + chalId);
                if(rawData) {
                    try {
                        console.log("Loading Challenge Data " + chalId + " from LocalStorage");
                        callback(year,level,JSON.parse(rawData));
                        return Promise.resolve("Loaded Data from LocalStorage");
                    } catch(e) {
                        console.log("Error parsing JSON data from localStorage: " + e.message);
                    }
                }
            }
        }

        // Remote Load
        return window.axios.get('/api/challenges/' + year + "/" + level)
            .then(function(response) {
                console.log("Data Fetched");
                if(hasLocalStorage) {
                    localStorage.setItem(CACHE_EXPIRE_PREFIX + chalId, Date.now() + CACHE_TIME);
                    localStorage.setItem(DATA_PREFIX + chalId, JSON.stringify(response.data));
                    console.log("Stored Challenge Data " + chalId);
                }
                return response.data;
            })
            .then((data) => {
                return callback(year,level,data);
            }).then(() => {
                return "Fetched Data via AJAX";
            })
            .catch(function(error) {
                console.log("Error loading data from remote: " + error);
            });
    }

}