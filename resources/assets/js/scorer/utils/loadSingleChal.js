export default class loadSingleChal {
    static load(chalId,callback) {
        // Remote Load
        return window.axios.get('/api/challenge/' + chalId)
            .then(function(response) {
                console.log("Single Challenge " + chalId + " Data Fetched");
                return response.data;
            })
            .then((data) => {
                return callback(data);
            }).then(() => {
                return "Fetched Data via AJAX";
            })
            .catch(function(error) {
                console.log("Error loading Single Challenge data from remote: " + error);
                throw "Error loading Single Challenge data from remote: " + error;
            });
    }

}