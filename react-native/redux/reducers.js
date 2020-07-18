import { combineReducers } from 'redux';

import {
  REQUEST_LOGIN,
  RECEIVE_LOGIN,
  REQUEST_LOGOUT,
  RECEIVE_LOGOUT,
  REQUEST_USER,
  RECEIVE_USER,
  ADD_COMIC,
  REMOVE_COMIC,
  UPDATE_ACCOUNT,
  REQUEST_COMICS,
  RECEIVE_COMICS,
  REQUEST_COMIC,
  RECEIVE_COMIC,
} from './actions';

let initialAuthState = {
  isFetching: false,
  didInvalidate: false,
  token: null,
  username: null,
  isAuthed: false,
  errors: [
  ],
};

let initialUserState = {
  isFetching: false,
  didInvalidate: false,
};

let initialComicsState = {
  isFetching: false,
  didInvalidate: false,
  comics: [
    {
      _id: '2345678dd',
      title: 'Test',
      strips: 10,
    }
  ],
};

let initialComicState = {};

function auth(state = initialAuthState, action) {
  switch(action.type) {
    case REQUEST_LOGIN:
      return {
        ...initialAuthState,
        isFetching: true,
        didInvalidate: false,
      };
    case RECEIVE_LOGIN:
      return {
        ...state,
        isFetching: false,
        didInvalidate: false,
        token: action.token,
        username: action.username,
        errors: action.token ? [] : action.errors,
        isAuthed: action.token ? true : false,
      };
    case REQUEST_LOGOUT:
      return {
        ...state,
        isFetching: true,
      };
    case RECEIVE_LOGOUT:
      return initialAuthState;
    default:
      return state;
  }
}

function user(state = initialUserState, action) {
  switch(action.type) {
    case REQUEST_USER:
      return {
        ...initialUserState,
        isFetching: true,
        didInvalidate: false,
      };
    case RECEIVE_USER:
      return {
        ...state,
        isFetching: false,
        didInvalidate: false,
        ...action.user
      };
    default:
      return state;
  }
}

function comics(state = initialComicsState, action) {
  switch(action.type) {
    case REQUEST_COMICS:
      return {
        ...state,
        isFetching: true,
        didInvalidate: false,
      };
    case RECEIVE_COMICS:
      return {
        ...state,
        isFetching: false,
        didInvalidate: false,
        comics: action.comics,
        lastUpdated: action.receivedAt
      };
    default:
      return state;
  }
}

function comic(state = initialComicState, action) {
  switch(action.type) {
    case REQUEST_COMIC:
      return {
        _id: action.comic_id,
        isFetching: true,
        didInvalidate: false,
      };
    case RECEIVE_COMIC:
      return {
        ...action.comic,
        isFetching: false,
        didInvalidate: false,
        lastUpdated: action.receivedAt,
      };
    default:
      return state;
  }
}

const rootReducer = combineReducers({auth, user, comics, comic});

export default rootReducer;
