import React, {useEffect} from 'react';
import {useDispatch, useSelector} from 'react-redux';

import {NavigationContainer} from "@react-navigation/native";
import {createDrawerNavigator, DrawerItem} from "@react-navigation/drawer";

import HomeScreen from "./screens/HomeScreen";
import LoginScreen from "./screens/LoginScreen";
import AccountScreen from "./screens/AccountScreen";
import ComicScreen from "./screens/ComicScreen";
import LogoutScreen from "./screens/LogoutScreen";

import {fetchComics} from "./redux/actions";

const Drawer = createDrawerNavigator();

const AppNavigator = function () {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchComics());
  }, []);

  const isAuthed = useSelector(state => state.auth.isAuthed);
  const comics = useSelector(state => state.comics.comics);

  return (
    <NavigationContainer>
      <Drawer.Navigator initialRouteName="Home">
        <Drawer.Screen name="Home" component={HomeScreen}/>

        {!isAuthed && (
          <Drawer.Screen name="Login" component={LoginScreen}/>
        )}
        {isAuthed && (
          <>
            <Drawer.Screen name="Account" component={AccountScreen}/>
            <Drawer.Screen name="Logout" component={LogoutScreen}/>
          </>
        )}

        {comics.map(comic => (
          <Drawer.Screen
            name={comic._id}
            key={comic._id}
            component={ComicScreen}
            initialParams={comic}
            options={{title: comic.title}}
          />
        ))}
      </Drawer.Navigator>
    </NavigationContainer>
  );
};

export default AppNavigator;
