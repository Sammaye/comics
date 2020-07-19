import React, {useEffect} from 'react';
import {useDispatch, useSelector} from 'react-redux';

import {NavigationContainer} from "@react-navigation/native";
import {createDrawerNavigator, DrawerItem} from "@react-navigation/drawer";

import HomeScreen from "./screens/HomeScreen";
import LoginScreen from "./screens/LoginScreen";
import SubscriptionScreen from "./screens/SubscriptionScreen";
import ComicScreen from "./screens/ComicScreen";
import LogoutScreen from "./screens/LogoutScreen";

import {fetchComics, receiveLogin} from "./redux/actions";

const Drawer = createDrawerNavigator();

const AppNavigator = function () {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchComics());
console.log('here');
    dispatch(receiveLogin(
      "sam.millman@gmail.com",
      "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI1ZjEyZWFiNjMyMmYwMTY5MjMyMTk3MjIiLCJqdGkiOiJiMmZmODJiNTdlMGEzMTcwYTA0MWZlNDUyNzY1MzE2ZDMyM2IzZDg4N2FiMTI3NmNmZDQ2MzdkODFiZGU3NzdiNzUwMGQ1MzJjZWJhM2E3NSIsImlhdCI6MTU5NTE0ODE2MywibmJmIjoxNTk1MTQ4MTYzLCJleHAiOjE2MjY2ODQxNjMsInN1YiI6IjVlZDQxMmY0NDRkMTA0MjA1NTc3ZmI0MyIsInNjb3BlcyI6WyIqIl19.fmzqxPchDMfYqCBpvhk6k6mZpt3dmUb_eaQ7nz75fftyXR3lLyHUcUU6aIa9GB4mZM2o0pIyUe2qPPQQi5kVieHpuC9hObch9CqAihbFhAwX6hw359vdPAHnhrjqcCMehb8BtOwEd1tqOJ0-pqu5mXilK-h-j08drpVjB9JgCnJ9TFse4LWEiKnorW4UtSy4WFIIp_YEJXB2nGiXWbSsQxzYA_tfRM_YfpZ0QCF4-oln7Him4IGgzkyhEg9wjiXoukVnG0zjm98JqRv-PeEvKNOV-c9H0dknYiDQOxQqOWg-khwfo88vw9eCsX1uFaOuX3AT-Sou6FihwOwyOYh-BPyDJHXMFYf4Lp8xlbC9wj88maK-X4FacdxJHRU2KOfI4MNmc0QUoiO4W_1QGyfznv4d-59BZwuxypBgAwvxmrsco-tU495lVdY5T8cBqZtXVqn3wqwtUF-FzXglNResbNZbLJYR6qXUuEuUTzjCy4bKX8WNYkcG0-AT5pqM_--8sQYs4OsXVXjdOQnrUsbnAyw7jGsrXzqo6xIrIODOQf7NdpaLerwZtBvke6yugzufw7hZneAcOb6wjM9-cWDs5oQv8USPmyNYU-iHcsY706kZrW4gCBdrj_7O2S2-J8eE9Kd0znY8xtBeBpzg6otDufwGEKRPs6VGqcSfk2GlDog"
    ));
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
            <Drawer.Screen name="Subscriptions" component={SubscriptionScreen}/>
            <Drawer.Screen name="Logout" component={LogoutScreen}/>
          </>
        )}

        {comics.map(comic => (
          <Drawer.Screen
            name={comic._id}
            key={comic._id}
            component={ComicScreen}
            initialParams={{_id: comic._id, comicIndex: comic.index}}
            options={{title: comic.title}}
          />
        ))}
      </Drawer.Navigator>
    </NavigationContainer>
  );
};

export default AppNavigator;
