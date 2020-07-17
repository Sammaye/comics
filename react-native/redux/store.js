import {applyMiddleware, createStore} from "redux";
import rootReducer from './reducers';
import thunk from "redux-thunk";
import {createLogger} from "redux-logger/src";

const middleware = [thunk];
if (process.env.NODE_ENV !== 'production') {
  middleware.push(createLogger());
}

const store = createStore(
  rootReducer,
  applyMiddleware(...middleware)
);

export default store;
