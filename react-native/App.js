/**
 * Sample React Native App
 * https://github.com/facebook/react-native
 *
 * @format
 * @flow strict-local
 */

import 'react-native-gesture-handler';

import React from 'react';
import {Provider as StoreProvider} from 'react-redux';
import store from "./redux/store";

import AppNavigator from "./AppNavigator";

const App: () => React$Node = () => {
  return (
    <StoreProvider store={store}>
      <AppNavigator />
    </StoreProvider>
  );
};

export default App;
