export const loadState = () => {
    try {
        console.log('Read state from storage');
        const serializedState = localStorage.getItem('state');
        if (serializedState === null) {
            return undefined;
        }
        return JSON.parse(serializedState);
    } catch(err) {
        return undefined;
    }
};

export const saveState = (state) => {
    try {
        console.log('Writing state to local storage');
        const serializedState = JSON.stringify(state);
        localStorage.setItem('state', serializedState);
    } catch (err) {
        // Ignore Write Errros
    }
};