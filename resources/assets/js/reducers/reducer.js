
export default function reducer(state = initialState, action) {
    // Actions go here!
    switch (action.type) {
        case 'change_url':
            return Object.assign({}, state, { backURL: action.url });
        case 'change_title':
            return Object.assign({}, state, {title: action.title });
        case 'load_chal_data':
            let temp = {};
            temp['challengeData'] = {};
            temp['challengeData'][action.year] = {};
            temp['challengeData'][action.year][action.level] = action.data;
            return Object.assign({}, state, temp);
        case 'save_favorite':
            return Object.assign({}, state, {
                teamFavorites: Object.assign({}, state.teamFavorites, { [action.id]: 1 })
            });
        case 'delete_favorite':
            let newTeamFavorites = Object.assign({}, state.teamFavorites);
            delete newTeamFavorites[action.id];
            return Object.assign({}, state, {
                teamFavorites: newTeamFavorites
            });
        default:
            return state;
    }
}


const initialState = {
    backURL: '/',
    title: 'Choose Competition',
    teamFavorites: {}
};
