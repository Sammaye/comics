import axios from 'axios';
import qs from 'querystring';

const BASE_URL = 'http://172.24.176.1:8000/';
const API_BASE_URL = `${BASE_URL}api/`;

export const REQUEST_LOGIN = 'REQUEST_LOGIN';
export const RECEIVE_LOGIN = 'RECEIVE_LOGIN';
export const REQUEST_LOGOUT = 'REQUEST_LOGOUT';
export const RECEIVE_LOGOUT = 'RECEIVE_LOGOUT';
export const REQUEST_USER = 'REQUEST_USER';
export const RECEIVE_USER = 'RECEIVE_USER';
export const ADD_COMIC = 'ADD_COMIC';
export const REMOVE_COMIC = 'REMOVE_COMIC';
export const UPDATE_ACCOUNT = 'UPDATE_ACCOUNT';
export const REQUEST_COMICS = 'REQUEST_COMICS';
export const RECEIVE_COMICS = 'RECEIVE_COMICS';
export const REQUEST_COMIC = 'REQUEST_COMIC';
export const RECEIVE_COMIC = 'RECEIVE_COMIC';

export function requestLogin(username, password) {
  return {
    type:  REQUEST_LOGIN,
    username,
    password,
  };
}

export function login(username, password) {
  return dispatch => {
    dispatch(requestLogin(username, password))

    return axios.post(`${BASE_URL}oauth/token`, qs.stringify({
      grant_type: 'password',
      client_id: '5f12eab6322f016923219722',
      client_secret: 'NCmAm30jCGcd43sKAkPrrS6EpmOdKISS8XkXrS2g',
      username: username,
      password: password,
      scope: '*',
    }), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
    }).then(response => {
      //console.log(response.data);
      dispatch(receiveLogin(username, response.data.access_token, []));
    }).catch(error => {
      //console.log(error.response);
      dispatch(receiveLogin(username, null, ['Username or password incorrect']));
    })
  }
}

export function receiveLogin(username, token, errors) {
  return {
    type: RECEIVE_LOGIN,
    username,
    token,
    errors,
  };
}

export function requestLogout() {
  return {
    type: REQUEST_LOGOUT,
  };
}

export function logout(token) {
  return dispatch => {
    dispatch(requestLogout())

    console.log(`${API_BASE_URL}logout`);
    return axios.post(`${API_BASE_URL}logout`, {}, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => dispatch(receiveLogout()));
  }
}

export function receiveLogout() {
  return {
    type: RECEIVE_LOGOUT,
  };
}

export function requestUser() {
  return {
    type: REQUEST_USER,
  };
}

export function user(token) {
  return dispatch => {
    dispatch(requestUser());

    return axios.get(`${API_BASE_URL}user`, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => dispatch(receiveUser(response.data)));
  }
}

export function receiveUser(user) {
  return {
    type: RECEIVE_USER,
    user,
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

    return axios.get(`${API_BASE_URL}comic/get-names`)
      .then(response => response.data)
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

    return axios.get(url)
      .then(response => response.data)
      .then(json => dispatch(receiveComic(json.comic)));
  }
}
