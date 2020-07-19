import { combineReducers } from 'redux';

import {
  REQUEST_LOGIN,
  RECEIVE_LOGIN,
  REQUEST_LOGOUT,
  RECEIVE_LOGOUT,
  REQUEST_USER,
  RECEIVE_USER,
  REQUEST_COMICS,
  RECEIVE_COMICS,
  REQUEST_COMIC,
  RECEIVE_COMIC,
  REQUEST_SUBSCRIPTIONS,
  RECEIVE_SUBSCRIPTIONS,
  REQUEST_ADD_SUBSCRIPTION,
  RECEIVE_ADD_SUBSCRIPTION,
  REQUEST_REMOVE_SUBSCRIPTION,
  RECEIVE_REMOVE_SUBSCRIPTION,
} from './actions';

let initialAuthState = {
  isFetching: true,
  didInvalidate: false,
  token: "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1ZjEyZWFiNjMyMmYwMTY5MjMyMTk3MjIiLCJqdGkiOiJiMmZmODJiNTdlMGEzMTcwYTA0MWZlNDUyNzY1MzE2ZDMyM2IzZDg4N2FiMTI3NmNmZDQ2MzdkODFiZGU3NzdiNzUwMGQ1MzJjZWJhM2E3NSIsImlhdCI6MTU5NTE0ODE2MywibmJmIjoxNTk1MTQ4MTYzLCJleHAiOjE2MjY2ODQxNjMsInN1YiI6IjVlZDQxMmY0NDRkMTA0MjA1NTc3ZmI0MyIsInNjb3BlcyI6WyIqIl19.fmzqxPchDMfYqCBpvhk6k6mZpt3dmUb_eaQ7nz75fftyXR3lLyHUcUU6aIa9GB4mZM2o0pIyUe2qPPQQi5kVieHpuC9hObch9CqAihbFhAwX6hw359vdPAHnhrjqcCMehb8BtOwEd1tqOJ0-pqu5mXilK-h-j08drpVjB9JgCnJ9TFse4LWEiKnorW4UtSy4WFIIp_YEJXB2nGiXWbSsQxzYA_tfRM_YfpZ0QCF4-oln7Him4IGgzkyhEg9wjiXoukVnG0zjm98JqRv-PeEvKNOV-c9H0dknYiDQOxQqOWg-khwfo88vw9eCsX1uFaOuX3AT-Sou6FihwOwyOYh-BPyDJHXMFYf4Lp8xlbC9wj88maK-X4FacdxJHRU2KOfI4MNmc0QUoiO4W_1QGyfznv4d-59BZwuxypBgAwvxmrsco-tU495lVdY5T8cBqZtXVqn3wqwtUF-FzXglNResbNZbLJYR6qXUuEuUTzjCy4bKX8WNYkcG0-AT5pqM_--8sQYs4OsXVXjdOQnrUsbnAyw7jGsrXzqo6xIrIODOQf7NdpaLerwZtBvke6yugzufw7hZneAcOb6wjM9-cWDs5oQv8USPmyNYU-iHcsY706kZrW4gCBdrj_7O2S2-J8eE9Kd0znY8xtBeBpzg6otDufwGEKRPs6VGqcSfk2GlDog",
  username: "sam.millman@gmail.com",
  isAuthed: true,
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

let initialComicState = {
  isFetching: false,
  didInvalidate: false,
};

let initialSubscriptionsState = {
  isFetching: false,
  didInvalidate: false,
  subscriptions: [],
};

let initialAddSubscriptionState = {
  isFetching: false,
  didInvalidate: false,
  comic_id: null,
  success: false,
};

let initialRemoveSubscriptionState = {
  isFetching: false,
  didInvalidate: false,
};

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

function subscriptions(state = initialSubscriptionsState, action) {
  switch(action.type) {
    case REQUEST_SUBSCRIPTIONS:
      return {
        ...initialSubscriptionsState,
        isFetching: true,
      };
    case RECEIVE_SUBSCRIPTIONS:
      return {
        ...initialSubscriptionsState,
        subscriptions: action.subscriptions,
      };
    default:
      return state;
  }
}

function addSubscription(state = initialAddSubscriptionState, action) {
  switch(action.type) {
    case REQUEST_ADD_SUBSCRIPTION:
      return {
        ...initialAddSubscriptionState,
        isFetching: true,
      };
    case RECEIVE_ADD_SUBSCRIPTION:
      return {
        ...initialAddSubscriptionState,
        comic_id: action.comic_id,
        success: action.success,
      };
    default:
      return state;
  }
}

function removeSubscription(state = initialRemoveSubscriptionState, action) {
  switch(action.type) {
    case REQUEST_REMOVE_SUBSCRIPTION:
      return {
        ...initialRemoveSubscriptionState,
        isFetching: true,
      };
    case RECEIVE_REMOVE_SUBSCRIPTION:
      return {
        ...initialRemoveSubscriptionState,
        comic_id: action.comic_id,
        success: action.success,
      };
    default:
      return state;
  }
}

const rootReducer = combineReducers({auth, user, comics, comic, subscriptions, addSubscription, removeSubscription});

export default rootReducer;
