const API_BASE_URL = 'http://172.29.16.1:8000/api/';

export const LOGIN = 'LOGIN';
export const LOGOUT = 'LOGOUT';
export const ADD_COMIC = 'ADD_COMIC';
export const REMOVE_COMIC = 'REMOVE_COMIC';
export const UPDATE_ACCOUNT = 'UPDATE_ACCOUNT';
export const REQUEST_COMICS = 'REQUEST_COMICS';
export const RECEIVE_COMICS = 'RECEIVE_COMICS';
export const REQUEST_COMIC = 'REQUEST_COMIC';
export const RECEIVE_COMIC = 'RECEIVE_COMIC';

export function login(token) {
  return {
    type: LOGIN,
  };
}

export function logout() {
  return {
    type: LOGOUT,
  };
}

export function addComic() {
  return {
    type: ADD_COMIC,
  };
}

export function removeComic() {
  return {
    type: REMOVE_COMIC,
  };
}

export function updateAccount() {
  return {
    type: UPDATE_ACCOUNT,
  };
}

export function requestComics() {
  return {
    type: REQUEST_COMICS,
  };
}

export function receiveComics(comics) {
  return {
    type: RECEIVE_COMICS,
    comics,
    receivedAt: Date.now(),
  };
}

export function requestComic(comic_id) {
  return {
    type: REQUEST_COMIC,
    comic_id
  };
}

export function receiveComic(comic) {
  return {
    type: RECEIVE_COMIC,
    comic,
    receivedAt: Date.now(),
  };
}

export function fetchComics() {
  return dispatch => {
    dispatch(requestComics());

    return fetch(`${API_BASE_URL}comic/get-names`)
      .then(response => response.json())
      .then(json => dispatch(receiveComics(json.comics)));
  }
}

export function fetchComic(comic_id, index) {
  return dispatch => {
    dispatch(requestComic(comic_id))

    let url = `${API_BASE_URL}comic/get/${comic_id}`
    if (typeof index !== 'undefined' && index) {
      url += `/${index}`;
    }

    return fetch(url, {
      method: 'GET',
    })
      .then(response => response.json())
      .then(json => dispatch(receiveComic(json.comic)));
  }
}
