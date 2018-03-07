
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
        default:
            return state;
    }
}

