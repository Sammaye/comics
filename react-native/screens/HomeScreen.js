import React, {useEffect} from 'react';
import {ScrollView, Text, View, StyleSheet} from "react-native";
import {useDispatch, useSelector} from "react-redux";
import {SafeAreaView} from "react-native-safe-area-context";
import Style from "../Style";
import {user} from "../redux/actions";

const HomeScreen = function () {
  const dispatch = useDispatch();

  const auth = useSelector(state => state.auth);
  const userData = useSelector(state => state.user);

  useEffect(() => {
    if (auth.isAuthed) {
      dispatch(user(auth.token));
    }
  }, [auth]);

  return (
    <SafeAreaView>
      <ScrollView contentContainerStyle={Style.screen} contentInsetAdjustmentBehavior="automatic">
        {auth.isAuthed && (<Text style={Style.h1}>Welcome, {userData.username}!</Text>)}
        {!auth.isAuthed && (<Text style={Style.h1}>Welcome, guest!</Text>)}
      </ScrollView>
    </SafeAreaView>
  );
}

export default HomeScreen;
