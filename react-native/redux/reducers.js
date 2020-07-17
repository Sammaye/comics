import { combineReducers } from 'redux';

import {
  LOGIN,
  LOGOUT,
  ADD_COMIC,
  REMOVE_COMIC,
  UPDATE_ACCOUNT,
  REQUEST_COMICS,
  RECEIVE_COMICS,
  REQUEST_COMIC,
  RECEIVE_COMIC,
} from './actions';

let initialUserState = {
  token: null,
  email: null,
  isAuthed: false,
  subscriptions: {},
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

function user(state = initialUserState, action) {
  switch(action.type) {
    case LOGIN:
      return {
        ...state,
        token: 'testtoken',
        email: 'test@test.com',
        isAuthed: true,
      };
    case LOGOUT:
      return {
        ...state,
        token: null,
        email: null,
        isAuthed: false,
      };
    case UPDATE_ACCOUNT:
    case ADD_COMIC:
    case REMOVE_COMIC:
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

const rootReducer = combineReducers({user, comics, comic});

export default rootReducer;
