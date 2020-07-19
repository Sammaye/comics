import axios from 'axios';
import qs from 'querystring';

const BASE_URL = 'http://172.21.160.1:8000/';
const API_BASE_URL = `${BASE_URL}api/`;

export const REQUEST_LOGIN = 'REQUEST_LOGIN';
export const RECEIVE_LOGIN = 'RECEIVE_LOGIN';
export const REQUEST_LOGOUT = 'REQUEST_LOGOUT';
export const RECEIVE_LOGOUT = 'RECEIVE_LOGOUT';
export const REQUEST_USER = 'REQUEST_USER';
export const RECEIVE_USER = 'RECEIVE_USER';
export const REQUEST_COMICS = 'REQUEST_COMICS';
export const RECEIVE_COMICS = 'RECEIVE_COMICS';
export const REQUEST_COMIC = 'REQUEST_COMIC';
export const RECEIVE_COMIC = 'RECEIVE_COMIC';
export const REQUEST_SUBSCRIPTIONS = 'REQUEST_SUBSCRIPTIONS';
export const RECEIVE_SUBSCRIPTIONS = 'RECEIVE_SUBSCRIPTIONS';
export const ADD_SUBSCRIPTIONS_SUBSCRIPTION = 'ADD_SUBSCRIPTIONS_SUBSCRIPTION';
export const REMOVE_SUBSCRIPTIONS_SUBSCRIPTION = 'REMOVE_SUBSCRIPTIONS_SUBSCRIPTION';
export const REQUEST_ADD_SUBSCRIPTION = 'REQUEST_ADD_SUBSCRIPTION';
export const RECEIVE_ADD_SUBSCRIPTION = 'RECEIVE_ADD_SUBSCRIPTION';
export const REQUEST_REMOVE_SUBSCRIPTION = 'REQUEST_REMOVE_SUBSCRIPTION';
export const RECEIVE_REMOVE_SUBSCRIPTION = 'RECEIVE_REMOVE_SUBSCRIPTION';

export function requestLogin(username, password) {
  return {
    type: REQUEST_LOGIN,
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

export function requestComics() {
  return {
    type: REQUEST_COMICS,
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

export function receiveComic(comic) {
  return {
    type: RECEIVE_COMIC,
    comic,
    receivedAt: Date.now(),
  };
}

export function requestSubscriptions() {
  return {
    type: REQUEST_SUBSCRIPTIONS,
  };
}

export function fetchSubscriptions(token, search) {
  return dispatch => {
    dispatch(requestSubscriptions());

    return axios.get(`${API_BASE_URL}subscriptions`, {
      params: {
        search
      },
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => dispatch(receiveSubscriptions(response.data.subscriptions)));
  }
}

export function receiveSubscriptions(subscriptions) {
  return {
    type: RECEIVE_SUBSCRIPTIONS,
    subscriptions,
  };
}

export function requestAddSubscription() {
  return {
    type: REQUEST_ADD_SUBSCRIPTION,
  };
}

export function addSubscription(token, comic_id) {
  return dispatch => {
    dispatch(requestAddSubscription());

    return axios.post(`${API_BASE_URL}comic/${comic_id}/subscribe`, {}, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => dispatch(receiveAddSubscription(response.data)));
  }
}

export function receiveAddSubscription(response) {
  return {
    type: RECEIVE_ADD_SUBSCRIPTION,
    comic_id:  response.comic_id,
    success: response.success,
  };
}

export function requestRemoveSubscription() {
  return {
    type: REQUEST_REMOVE_SUBSCRIPTION,
  };
}

export function removeSubscription(token, comic_id) {
  return dispatch => {
    dispatch(requestRemoveSubscription());

    return axios.post(`${API_BASE_URL}comic/${comic_id}/unsubscribe`, {}, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    })
      .then(response => dispatch(receiveRemoveSubscription(response.data)));
  }
}

export function receiveRemoveSubscription(response) {
  return {
    type: RECEIVE_REMOVE_SUBSCRIPTION,
    comic_id: response.comic_id,
    success: response.success,
  };
}

export function addSubscriptionsSubscription(comic_id) {
  return {
    type: ADD_SUBSCRIPTIONS_SUBSCRIPTION,
    comic_id,
  }
}

export function removeSubscriptionsSubscription(comic_id) {
  return {
    type: REMOVE_SUBSCRIPTIONS_SUBSCRIPTION,
    comic_id,
  };
}
