// Action Types
export const UPDATE_BACK_BUTTON = 'UPDATE_BACK_BUTTON';
export const UPDATE_PAGE_TITLE = 'UPDATE_PAGE_TITLE';

export function updateBackButton(newURL) {
    return { type: UPDATE_BACK_BUTTON, newURL};
}

export function updatePageTitle(newTitle) {
    return {type: UPDATE_PAGE_TITLE, newTitle };
}