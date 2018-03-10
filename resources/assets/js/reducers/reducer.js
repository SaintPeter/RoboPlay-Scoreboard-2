
const initialState = {
    challengeData: {},
    backURL: '/',
    title: 'Choose Competition'
};

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
        default:
            return state;
    }
}

