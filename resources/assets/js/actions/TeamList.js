export const SAVE_FAVORITE = 'SAVE_FAVORITE';
export const DELETE_FAVORITE = 'DELETE_FAVORITE';

export function saveFavorite(id) {
    return {
        type: SAVE_FAVORITE,
        id
    };
}

export function deleteFavorite(id) {
    return {
        type: DELETE_FAVORITE,
        id
    };
}